"use client";

import { ChevronDown, RotateCcw } from "lucide-react";
import { useTranslations } from "next-intl";

function HeroFilterSelect({ label }: { label: string }) {
  return (
    <button
      type="button"
      className="inline-flex min-w-[140px] items-center justify-between gap-4 border-b border-white/70 pb-3 text-sm font-semibold text-white transition-colors hover:border-white sm:min-w-[180px] sm:text-base"
    >
      <span>{label}</span>
      <ChevronDown className="size-4 shrink-0 text-white/80" />
    </button>
  );
}

export function ResourcesHeroFilters() {
  const t = useTranslations("resources.filters");

  return (
    <div className="flex flex-wrap items-end justify-start gap-4 sm:gap-6">
      <HeroFilterSelect label={t("resourceType")} />
      <HeroFilterSelect label={t("focusArea")} />
      <HeroFilterSelect label={t("year")} />

      <button
        type="button"
        className="rounded-xl bg-primary px-8 py-3 text-sm font-semibold text-white transition-colors hover:bg-primary/90 sm:text-base"
      >
        {t("search")}
      </button>

      <button
        type="button"
        aria-label={t("reset")}
        className="inline-flex size-11 items-center justify-center rounded-xl border border-white/30 bg-white/10 text-white transition-colors hover:bg-white/20"
      >
        <RotateCcw className="size-4" />
      </button>
    </div>
  );
}
