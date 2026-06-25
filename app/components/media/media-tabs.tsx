"use client";

import { Link, usePathname } from "@/i18n/routing";
import { cn } from "@/lib/utils";
import type { MediaTab } from "@/lib/media";
import { ChevronDown, RotateCcw, Search } from "lucide-react";
import { useTranslations } from "next-intl";

type Tab = {
  id: MediaTab;
  label: string;
  href:
    | "/media/news"
    | "/media/newsletter"
    | "/media/city-meetings"
    | "/media/secretary-speaks";
};

type Props = {
  tabs: Tab[];
  activeTab: MediaTab;
};

function FilterButton({
  label,
  className,
}: {
  label: string;
  className?: string;
}) {
  return (
    <button
      type="button"
      className={cn(
        "inline-flex items-center justify-between gap-3 rounded-xl bg-[#e8f4f8] px-4 py-2.5 text-sm font-semibold text-secondary transition-colors hover:bg-[#d9edf4]",
        className,
      )}
    >
      <span>{label}</span>
      <ChevronDown className="size-4 shrink-0 text-secondary/70" />
    </button>
  );
}

function IconFilterButton({
  label,
  children,
}: {
  label: string;
  children: React.ReactNode;
}) {
  return (
    <button
      type="button"
      aria-label={label}
      className="inline-flex size-11 items-center justify-center rounded-xl bg-[#e8f4f8] text-secondary transition-colors hover:bg-[#d9edf4]"
    >
      {children}
    </button>
  );
}

export function MediaTabs({ tabs, activeTab }: Props) {
  const pathname = usePathname();
  const t = useTranslations("media.filters");
  const showFilters = activeTab === "news";

  return (
    <div className="relative z-20 -mt-8 px-4 sm:-mt-10 sm:px-6">
      <div className="mx-auto max-w-7xl">
        <nav
          aria-label="Media center tabs"
          className="flex w-full overflow-hidden rounded-2xl border border-[#dfe6ee] bg-white shadow-[0_10px_40px_rgba(0,112,158,0.14)]"
        >
          {tabs.map((tab, index) => {
            const isActive =
              activeTab === tab.id || pathname.startsWith(tab.href);

            return (
              <div key={tab.id} className="flex flex-1 items-stretch">
                {index > 0 ? (
                  <span
                    className="my-5 w-px shrink-0 self-stretch bg-[#dfe6ee]"
                    aria-hidden
                  />
                ) : null}

                <Link
                  href={tab.href}
                  aria-current={isActive ? "page" : undefined}
                  className="flex flex-1 items-center justify-center px-4 py-5 text-sm font-semibold text-secondary transition-colors hover:text-primary sm:px-6 sm:py-6 sm:text-base"
                >
                  <span className="inline-flex items-center gap-2">
                    <span
                      className={cn(
                        "border-b-2 border-transparent pb-1",
                        isActive && "border-secondary",
                      )}
                    >
                      {tab.label}
                    </span>
                    {isActive ? (
                      <span
                        className="size-1.5 shrink-0 rounded-full bg-primary"
                        aria-hidden
                      />
                    ) : null}
                  </span>
                </Link>
              </div>
            );
          })}
        </nav>

        {showFilters ? (
          <div className="mt-4 flex flex-wrap items-center justify-start gap-3">
            <FilterButton label={t("year")} className="min-w-[140px] sm:min-w-[160px]" />
            <FilterButton label={t("month")} className="min-w-[140px] sm:min-w-[160px]" />
            <IconFilterButton label={t("reset")}>
              <RotateCcw className="size-4" />
            </IconFilterButton>
            <IconFilterButton label={t("search")}>
              <Search className="size-4" />
            </IconFilterButton>
          </div>
        ) : null}
      </div>
    </div>
  );
}
