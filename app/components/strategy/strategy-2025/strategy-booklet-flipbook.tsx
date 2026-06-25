"use client";

import {
  getDisplayedPageNumber,
  loadPdfPageImages,
  revokePdfPageImages,
  toRtlFlipIndex,
} from "@/lib/pdf-pages";
import { DialogWrapper } from "@/app/components/ui/dialog-wrapper";
import { cn } from "@/lib/utils";
import {
  ChevronLeft,
  ChevronRight,
  Download,
  Grid3x3,
  Loader2,
  Maximize,
  Minimize,
  Minus,
  MoreHorizontal,
  Plus,
  Share2,
} from "lucide-react";
import {
  forwardRef,
  useCallback,
  useEffect,
  useMemo,
  useRef,
  useState,
} from "react";
import HTMLFlipBook from "react-pageflip";

type FlipbookLabels = {
  loading: string;
  loadError: string;
  prev: string;
  next: string;
  zoomIn: string;
  zoomOut: string;
  fullscreen: string;
  exitFullscreen: string;
  share: string;
  thumbnails: string;
  download: string;
  more: string;
  page: string;
  shareCopied: string;
};

type Props = {
  pdfUrl: string;
  downloadUrl: string;
  isRtl: boolean;
  pageTitle: string;
  labels: FlipbookLabels;
};

type FlipbookPageProps = {
  src: string;
  pageNumber: number;
};

const FlipbookPage = forwardRef<HTMLDivElement, FlipbookPageProps>(
  function FlipbookPage({ src, pageNumber }, ref) {
    return (
      <div
        ref={ref}
        className="h-full w-full overflow-hidden bg-white shadow-sm"
        data-density="soft"
      >
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          src={src}
          alt=""
          draggable={false}
          className="block h-full w-full object-cover"
          data-page={pageNumber}
        />
      </div>
    );
  },
);

type PageFlipApi = {
  flipNext: () => void;
  flipPrev: () => void;
  flip: (page: number) => void;
  getCurrentPageIndex: () => number;
};

export function StrategyBookletFlipbook({
  pdfUrl,
  downloadUrl,
  isRtl,
  pageTitle,
  labels,
}: Props) {
  const bookRef = useRef<{ pageFlip: () => PageFlipApi } | null>(null);
  const viewerRef = useRef<HTMLDivElement>(null);

  const [pages, setPages] = useState<string[]>([]);
  const [pageSize, setPageSize] = useState({ width: 420, height: 594 });
  const [loading, setLoading] = useState(true);
  const [loadProgress, setLoadProgress] = useState(0);
  const [loadError, setLoadError] = useState<string | null>(null);
  const [flipIndex, setFlipIndex] = useState(0);
  const [zoom, setZoom] = useState(1);
  const [isFullscreen, setIsFullscreen] = useState(false);
  const [showThumbnails, setShowThumbnails] = useState(false);
  const [showMoreMenu, setShowMoreMenu] = useState(false);
  const [shareMessage, setShareMessage] = useState<string | null>(null);

  const displayPages = useMemo(
    () => (isRtl ? [...pages].reverse() : pages),
    [isRtl, pages],
  );

  useEffect(() => {
    let cancelled = false;

    async function loadPdf() {
      setLoading(true);
      setLoadProgress(0);
      setLoadError(null);
      setPages([]);
      setFlipIndex(isRtl ? 0 : 0);

      try {
        const loadedPages: string[] = [];

        await loadPdfPageImages(
          pdfUrl,
          (loaded, total) => {
            if (!cancelled) {
              setLoadProgress(Math.round((loaded / total) * 100));
            }
          },
          (image, index, total) => {
            if (cancelled) {
              return;
            }

            loadedPages.push(image.src);

            if (index === 0) {
              const aspectRatio = image.height / image.width;
              setPageSize({
                width: 420,
                height: Math.round(420 * aspectRatio),
              });
            }

            if (index === total - 1) {
              setFlipIndex(isRtl ? total - 1 : 0);
            }
          },
        );

        if (!cancelled) {
          setPages(loadedPages);
          setLoading(false);
        }
      } catch (error) {
        if (!cancelled) {
          setPages([]);
          setLoadError(
            error instanceof Error ? error.message : labels.loadError,
          );
          setLoading(false);
        }
      }
    }

    void loadPdf();

    return () => {
      cancelled = true;
      setPages((currentPages) => {
        revokePdfPageImages(currentPages);
        return [];
      });
    };
  }, [isRtl, labels.loadError, pdfUrl]);

  useEffect(() => {
    function onFullscreenChange() {
      setIsFullscreen(Boolean(document.fullscreenElement));
    }

    document.addEventListener("fullscreenchange", onFullscreenChange);
    return () => {
      document.removeEventListener("fullscreenchange", onFullscreenChange);
    };
  }, []);

  const totalPages = pages.length;
  const displayedPage = getDisplayedPageNumber(flipIndex, isRtl, totalPages);
  const canGoBackward = isRtl ? flipIndex < totalPages - 1 : flipIndex > 0;
  const canGoForward = isRtl ? flipIndex > 0 : flipIndex < totalPages - 1;

  const goBackward = useCallback(() => {
    if (isRtl) {
      bookRef.current?.pageFlip().flipNext();
      return;
    }

    bookRef.current?.pageFlip().flipPrev();
  }, [isRtl]);

  const goForward = useCallback(() => {
    if (isRtl) {
      bookRef.current?.pageFlip().flipPrev();
      return;
    }

    bookRef.current?.pageFlip().flipNext();
  }, [isRtl]);

  const flipTo = useCallback(
    (pageNumber: number) => {
      const targetIndex = isRtl
        ? toRtlFlipIndex(pageNumber - 1, totalPages)
        : pageNumber - 1;

      bookRef.current?.pageFlip().flip(targetIndex);
      setShowThumbnails(false);
    },
    [isRtl, totalPages],
  );

  const toggleFullscreen = useCallback(async () => {
    if (!viewerRef.current) {
      return;
    }

    if (document.fullscreenElement) {
      await document.exitFullscreen();
      return;
    }

    await viewerRef.current.requestFullscreen();
  }, []);

  const handleShare = useCallback(async () => {
    const shareUrl = window.location.href.split("#")[0] + "#strategy-booklet";

    try {
      if (navigator.share) {
        await navigator.share({
          title: document.title,
          url: shareUrl,
        });
        return;
      }

      await navigator.clipboard.writeText(shareUrl);
      setShareMessage(labels.shareCopied);
      window.setTimeout(() => setShareMessage(null), 2500);
    } catch {
      // User cancelled share or clipboard unavailable.
    }
  }, [labels.shareCopied]);

  const pageLabel = labels.page
    .replace("{current}", String(displayedPage))
    .replace("{total}", String(totalPages));

  if (loading) {
    return (
      <div className="flex min-h-[420px] flex-col items-center justify-center gap-4 rounded-3xl bg-white/70 px-6 py-16">
        <Loader2 className="size-10 animate-spin text-primary" aria-hidden />
        <p className="text-base font-medium text-secondary">{labels.loading}</p>
        <p className="text-sm font-medium text-primary tabular-nums">
          {loadProgress}%
        </p>
      </div>
    );
  }

  if (!pages.length) {
    return (
      <div className="rounded-3xl bg-white/70 px-6 py-16 text-center text-secondary">
        {loadError ?? labels.loadError}
      </div>
    );
  }

  return (
    <div
      ref={viewerRef}
      dir={isRtl ? "rtl" : "ltr"}
      className={cn(
        "strategy-booklet relative overflow-hidden rounded-3xl bg-[#d9ebf3] px-3 py-8 sm:px-8 sm:py-10",
        isFullscreen && "flex min-h-screen flex-col justify-center bg-[#d9ebf3]",
      )}
    >
      <div className="relative mx-auto flex max-w-6xl items-center justify-center">
        <button
          type="button"
          onClick={goBackward}
          disabled={!canGoBackward}
          aria-label={labels.prev}
          className={cn(
            "absolute z-20 hidden size-12 items-center justify-center rounded-full text-secondary transition-opacity sm:flex",
            isRtl ? "left-0 sm:left-2" : "right-0 sm:right-2",
            canGoBackward
              ? "opacity-80 hover:opacity-100"
              : "cursor-not-allowed opacity-20",
          )}
        >
          <ChevronLeft className="size-10" strokeWidth={1.5} />
        </button>

        <div
          className="mx-auto w-full max-w-5xl overflow-hidden transition-transform duration-300"
          style={{
            transform: `scale(${zoom})`,
            transformOrigin: "center center",
          }}
        >
          <HTMLFlipBook
            key={`${isRtl ? "rtl" : "ltr"}-${totalPages}`}
            ref={bookRef}
            width={pageSize.width}
            height={pageSize.height}
            size="stretch"
            minWidth={280}
            maxWidth={900}
            minHeight={380}
            maxHeight={1200}
            showCover
            usePortrait
            startPage={isRtl ? totalPages - 1 : 0}
            mobileScrollSupport={false}
            drawShadow
            maxShadowOpacity={0.35}
            flippingTime={700}
            renderOnlyPageLengthChange
            className="mx-auto"
            onFlip={(event) => setFlipIndex(event.data)}
          >
            {displayPages.map((src, index) => {
              const pageNumber = isRtl ? totalPages - index : index + 1;

              return (
                <FlipbookPage
                  key={`page-${pageNumber}`}
                  src={src}
                  pageNumber={pageNumber}
                />
              );
            })}
          </HTMLFlipBook>
        </div>

        <button
          type="button"
          onClick={goForward}
          disabled={!canGoForward}
          aria-label={labels.next}
          className={cn(
            "absolute z-20 hidden size-12 items-center justify-center rounded-full text-secondary transition-opacity sm:flex",
            isRtl ? "right-0 sm:right-2" : "left-0 sm:left-2",
            canGoForward
              ? "opacity-80 hover:opacity-100"
              : "cursor-not-allowed opacity-20",
          )}
        >
          <ChevronRight className="size-10" strokeWidth={1.5} />
        </button>
      </div>

      <div className="relative z-30 mx-auto mt-6 flex justify-center sm:mt-8">
        <div className="inline-flex items-center gap-1 rounded-full bg-secondary px-3 py-2 text-white shadow-lg sm:gap-2 sm:px-4">
          <span className="min-w-14 px-2 text-center text-sm font-medium tabular-nums">
            {pageLabel}
          </span>

          <ToolbarButton
            label={labels.thumbnails}
            onClick={() => {
              setShowThumbnails((value) => !value);
              setShowMoreMenu(false);
            }}
            active={showThumbnails}
          >
            <Grid3x3 className="size-4" />
          </ToolbarButton>

          <ToolbarButton
            label={labels.zoomOut}
            onClick={() => setZoom((value) => Math.max(0.75, value - 0.1))}
            disabled={zoom <= 0.75}
          >
            <Minus className="size-4" />
          </ToolbarButton>

          <ToolbarButton
            label={labels.zoomIn}
            onClick={() => setZoom((value) => Math.min(1.5, value + 0.1))}
            disabled={zoom >= 1.5}
          >
            <Plus className="size-4" />
          </ToolbarButton>

          <ToolbarButton
            label={isFullscreen ? labels.exitFullscreen : labels.fullscreen}
            onClick={() => void toggleFullscreen()}
          >
            {isFullscreen ? (
              <Minimize className="size-4" />
            ) : (
              <Maximize className="size-4" />
            )}
          </ToolbarButton>

          <ToolbarButton label={labels.share} onClick={() => void handleShare()}>
            <Share2 className="size-4" />
          </ToolbarButton>

          <div className="relative">
            <ToolbarButton
              label={labels.more}
              onClick={() => {
                setShowMoreMenu((value) => !value);
                setShowThumbnails(false);
              }}
              active={showMoreMenu}
            >
              <MoreHorizontal className="size-4" />
            </ToolbarButton>

            {showMoreMenu ? (
              <div
                className={cn(
                  "absolute bottom-full mb-3 min-w-44 rounded-xl bg-white p-2 text-secondary shadow-xl",
                  isRtl ? "left-0" : "right-0",
                )}
              >
                <a
                  href={downloadUrl}
                  download
                  className="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent"
                >
                  <Download className="size-4" />
                  {labels.download}
                </a>
              </div>
            ) : null}
          </div>
        </div>
      </div>

      {shareMessage ? (
        <p className="mt-3 text-center text-sm font-medium text-secondary">
          {shareMessage}
        </p>
      ) : null}

      <DialogWrapper
        open={showThumbnails}
        onOpenChange={setShowThumbnails}
        size="xl"
        className="max-w-5xl"
        header={{ mainTitle: pageTitle }}
        scrollableContent
        maxScrollHeight="70vh"
        content={
          <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
            {pages.map((src, index) => (
              <button
                key={`thumb-${index + 1}`}
                type="button"
                onClick={() => flipTo(index + 1)}
                className={cn(
                  "overflow-hidden rounded-lg border-2 bg-white transition",
                  displayedPage === index + 1
                    ? "border-primary"
                    : "border-transparent hover:border-primary/40",
                )}
              >
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img
                  src={src}
                  alt=""
                  className="aspect-3/4 w-full object-cover"
                />
                <span className="block py-1 text-center text-xs font-medium text-secondary">
                  {index + 1}
                </span>
              </button>
            ))}
          </div>
        }
      />
    </div>
  );
}

function ToolbarButton({
  children,
  label,
  onClick,
  disabled,
  active,
}: {
  children: React.ReactNode;
  label: string;
  onClick: () => void;
  disabled?: boolean;
  active?: boolean;
}) {
  return (
    <button
      type="button"
      aria-label={label}
      title={label}
      disabled={disabled}
      onClick={onClick}
      className={cn(
        "flex size-9 items-center justify-center rounded-full transition",
        active ? "bg-white/20" : "hover:bg-white/15",
        disabled && "cursor-not-allowed opacity-40",
      )}
    >
      {children}
    </button>
  );
}
