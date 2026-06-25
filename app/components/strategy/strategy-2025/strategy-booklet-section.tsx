"use client";

import dynamic from "next/dynamic";
import { Loader2 } from "lucide-react";

const StrategyBookletFlipbook = dynamic(
  () =>
    import("@/app/components/strategy/strategy-2025/strategy-booklet-flipbook").then(
      (module) => module.StrategyBookletFlipbook,
    ),
  {
    ssr: false,
    loading: () => (
      <div className="flex min-h-[420px] flex-col items-center justify-center gap-4 rounded-3xl bg-white/70 px-6 py-16">
        <Loader2 className="size-10 animate-spin text-primary" aria-hidden />
      </div>
    ),
  },
);

type Props = {
  title: string;
  pageTitle: string;
  pdfUrl: string;
  downloadUrl: string;
  isRtl: boolean;
  labels: {
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
};

export function StrategyBookletSection({
  title,
  pageTitle,
  pdfUrl,
  downloadUrl,
  isRtl,
  labels,
}: Props) {
  return (
    <section
      id="strategy-booklet"
      dir={isRtl ? "rtl" : "ltr"}
      className="scroll-mt-24 bg-accent px-4 py-12 sm:px-6 sm:py-16 lg:py-20"
    >
      <div className="mx-auto max-w-7xl space-y-8">
        <h2 className="text-center text-2xl font-bold text-secondary sm:text-3xl lg:text-4xl">
          {title}
        </h2>

        <StrategyBookletFlipbook
          pdfUrl={pdfUrl}
          downloadUrl={downloadUrl}
          isRtl={isRtl}
          pageTitle={pageTitle}
          labels={labels}
        />
      </div>
    </section>
  );
}
