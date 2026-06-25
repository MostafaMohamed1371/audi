import * as pdfjs from "pdfjs-dist";

let workerConfigured = false;

function configurePdfWorker() {
  if (workerConfigured || typeof window === "undefined") {
    return;
  }

  pdfjs.GlobalWorkerOptions.workerSrc = "/pdf.worker.min.mjs";
  workerConfigured = true;
}

export type PdfPageImage = {
  src: string;
  width: number;
  height: number;
};

async function renderPageImage(
  page: pdfjs.PDFPageProxy,
): Promise<PdfPageImage> {
  const baseViewport = page.getViewport({ scale: 1 });
  const scale = Math.min(1.25, 1000 / baseViewport.width);
  const viewport = page.getViewport({ scale });

  const canvas = document.createElement("canvas");
  canvas.width = viewport.width;
  canvas.height = viewport.height;

  const context = canvas.getContext("2d");
  if (!context) {
    throw new Error("Unable to create canvas context for PDF rendering.");
  }

  await page.render({ canvas, canvasContext: context, viewport }).promise;

  const blob = await new Promise<Blob>((resolve, reject) => {
    canvas.toBlob(
      (value) => {
        if (value) {
          resolve(value);
          return;
        }

        reject(new Error("Unable to encode PDF page."));
      },
      "image/jpeg",
      0.82,
    );
  });

  return {
    src: URL.createObjectURL(blob),
    width: viewport.width,
    height: viewport.height,
  };
}

export async function loadPdfPageImages(
  pdfUrl: string,
  onProgress?: (loaded: number, total: number) => void,
  onPage?: (image: PdfPageImage, index: number, total: number) => void,
): Promise<PdfPageImage[]> {
  configurePdfWorker();

  const pdf = await pdfjs.getDocument({ url: pdfUrl }).promise;
  const images: PdfPageImage[] = [];

  for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber += 1) {
    const page = await pdf.getPage(pageNumber);
    const image = await renderPageImage(page);

    images.push(image);
    onPage?.(image, pageNumber - 1, pdf.numPages);
    onProgress?.(pageNumber, pdf.numPages);
  }

  return images;
}

export function revokePdfPageImages(images: string[]) {
  for (const src of images) {
    if (src.startsWith("blob:")) {
      URL.revokeObjectURL(src);
    }
  }
}

export function getDisplayedPageNumber(
  flipIndex: number,
  isRtl = false,
  totalPages = 0,
) {
  const index =
    isRtl && totalPages > 0 ? totalPages - 1 - flipIndex : flipIndex;

  if (index <= 0) {
    return 1;
  }

  return index * 2;
}

export function toRtlFlipIndex(pageIndex: number, totalPages: number) {
  return totalPages - 1 - pageIndex;
}
