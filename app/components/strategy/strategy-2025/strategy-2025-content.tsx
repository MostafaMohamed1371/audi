import { Strategy2025Hero } from "@/app/components/strategy/strategy-2025/strategy-2025-hero";
import { StrategyBookletSection } from "@/app/components/strategy/strategy-2025/strategy-booklet-section";
import { StrategyDiagramSection } from "@/app/components/strategy/strategy-2025/strategy-diagram-section";
import { StrategyPillarsSection } from "@/app/components/strategy/strategy-2025/strategy-pillars-section";
import { fetchStrategy2025 } from "@/lib/api";
import { getLocale, getTranslations } from "next-intl/server";

type DiagramItem = {
  id: string;
  title: string;
  content?: string;
  columns?: string[];
};

export async function Strategy2025Content() {
  const pagesT = await getTranslations("strategy.pages");
  const t = await getTranslations("strategy.strategy2025");
  const diagramT = await getTranslations("strategy.diagram");
  const locale = await getLocale();
  const isRtl = locale === "ar";
  const apiData = await fetchStrategy2025(locale);

  const pillars = apiData?.pillars ?? (t.raw("pillars") as {
    number: string;
    text: string;
  }[]);

  const diagramItems = apiData?.diagram.items ?? (diagramT.raw("items") as DiagramItem[]);
  const diagramRows = diagramT.raw("rows") as Array<{
    type: "split";
    leftId: string;
    rightId: string;
    leftSpan: 1 | 2;
    rightSpan: 1 | 2;
  }>;
  const enablersId = diagramT("enablersId");

  const vision =
    diagramItems.find((item) => item.id === "vision") ??
    ({ id: "vision", title: "", content: "" } satisfies DiagramItem);
  const enablers =
    diagramItems.find((item) => item.id === enablersId) ??
    ({ id: enablersId, title: "", content: "" } satisfies DiagramItem);
  const gridItems = diagramItems.filter(
    (item) => item.id !== "vision" && item.id !== enablersId,
  );

  return (
    <div className="bg-white">
      <Strategy2025Hero
        title={pagesT("strategy2025")}
        bookletLabel={t("booklet")}
        bookletHref={t("bookletHref")}
        isRtl={isRtl}
        image="/header/2.png"
        backgroundColor="#000000B8"
      />

      <section
        dir={isRtl ? "rtl" : "ltr"}
        className="bg-white px-4 py-12 sm:px-6 sm:py-16 lg:py-20"
      >
        <div className="mx-auto max-w-7xl space-y-4 text-start">
          <h2 className="text-xl font-bold leading-snug text-secondary sm:text-2xl lg:text-3xl">
            {apiData?.introTitle ?? t("introTitle")}
          </h2>
          <p className="text-lg font-medium text-primary sm:text-xl">
            {apiData?.introSubtitle ?? t("introSubtitle")}
          </p>
        </div>
      </section>

      <StrategyPillarsSection pillars={pillars} isRtl={isRtl} />

      <StrategyDiagramSection
        title={diagramT("title")}
        vision={vision}
        enablers={enablers}
        items={gridItems}
        rows={diagramRows}
        placeholder={diagramT("placeholder")}
        isRtl={isRtl}
      />
      
      <StrategyBookletSection
        title={apiData?.booklet.title ?? t("booklet")}
        pageTitle={pagesT("strategy2025")}
        pdfUrl={apiData?.booklet.pdfUrl ?? t("bookletPdf")}
        downloadUrl={apiData?.booklet.pdfUrl ?? t("bookletPdf")}
        isRtl
        labels={{
          loading: t("bookletLoading"),
          loadError: t("bookletLoadError"),
          prev: t("bookletPrev"),
          next: t("bookletNext"),
          zoomIn: t("bookletZoomIn"),
          zoomOut: t("bookletZoomOut"),
          fullscreen: t("bookletFullscreen"),
          exitFullscreen: t("bookletExitFullscreen"),
          share: t("bookletShare"),
          thumbnails: t("bookletThumbnails"),
          download: t("bookletDownload"),
          more: t("bookletMore"),
          page: t("bookletPage"),
          shareCopied: t("bookletShareCopied"),
        }}
      />

    </div>
  );
}
