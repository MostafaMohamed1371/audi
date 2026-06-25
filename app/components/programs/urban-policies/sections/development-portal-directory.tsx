"use client";

import type { PortalDirectoryContent } from "@/app/components/programs/urban-policies/shared/types";
import type { PortalDirectoryTab } from "@/lib/programs-urban-policies-directory";
import { cn } from "@/lib/utils";
import { ChevronDown, List, MapPin, RotateCcw, Search } from "lucide-react";
import { useCallback, useState } from "react";

type Props = {
  content: PortalDirectoryContent;
  activeTab: PortalDirectoryTab;
  isRtl: boolean;
  onTabChange: (tab: PortalDirectoryTab) => void;
};

function FilterSelect({ label, isRtl }: { label: string; isRtl: boolean }) {
  return (
    <label className="block">
      <span className="mb-2 block text-sm font-medium text-secondary">
        {label}
      </span>
      <div className="relative">
        <select
          dir={isRtl ? "rtl" : "ltr"}
          className="h-11 w-full appearance-none rounded-lg border border-[#00709E26] bg-[#e8f4f8] px-4 text-sm text-secondary outline-none focus:border-primary"
          defaultValue=""
        >
          <option value="" disabled>
            {label}
          </option>
        </select>
        <ChevronDown className="pointer-events-none absolute top-1/2 size-4 -translate-y-1/2 text-primary inset-e-3" />
      </div>
    </label>
  );
}

export function DevelopmentPortalDirectory({
  content,
  activeTab,
  isRtl,
  onTabChange,
}: Props) {
  const [viewMode, setViewMode] = useState<"list" | "map">("list");

  const resetFilters = useCallback(() => {}, []);

  const isProjects = activeTab === "projects";
  const rows = content.rows[activeTab] ?? [];

  return (
    <div className="bg-[#f4f6f8]">
      <div className="bg-[#eef1f4] px-4 py-6 sm:px-6 sm:py-8">
        <div className="mx-auto max-w-7xl overflow-hidden rounded-2xl bg-white shadow-[0_8px_32px_rgba(17,31,66,0.08)]">
          <video
            className="aspect-1468/703 w-full object-cover"
            controls
            playsInline
            preload="metadata"
            poster={content.videoPoster}
          >
            <source src={content.video} type="video/mp4" />
          </video>
        </div>
      </div>

      <div className="mx-auto max-w-7xl px-4 pb-16 sm:px-6 sm:pb-20">
        <div
          dir={isRtl ? "rtl" : "ltr"}
          className="mb-8 space-y-3 text-center sm:mb-10"
        >
          <h2 className="text-2xl font-bold text-secondary sm:text-3xl lg:text-4xl">
            {content.title}
          </h2>
          <p className="mx-auto max-w-4xl text-sm leading-7 text-[#4d5a6f] sm:text-base sm:leading-8">
            {content.subtitle}
          </p>
        </div>

        <div
          dir={isRtl ? "rtl" : "ltr"}
          className="grid items-start gap-6 lg:grid-cols-12 lg:gap-8"
        >
          <aside className="order-1 h-fit rounded-2xl bg-white p-5 shadow-[1px_1px_18.6px_0px_#111F421C] sm:p-6 lg:order-2 lg:col-span-4">
            <h3 className="mb-5 text-lg font-bold text-secondary">
              {content.filtersTitle}
            </h3>
            <div className="space-y-4">
              <FilterSelect label={content.countryLabel} isRtl={isRtl} />
              <FilterSelect label={content.cityLabel} isRtl={isRtl} />
              <FilterSelect label={content.citySizeLabel} isRtl={isRtl} />
            </div>
            <button
              type="button"
              onClick={resetFilters}
              className="mt-5 flex w-full items-center justify-center gap-2 rounded-lg bg-secondary px-4 py-3 text-sm font-bold text-white transition-colors hover:bg-secondary/90"
            >
              <RotateCcw className="size-4" />
              {content.resetLabel}
            </button>
            <button
              type="button"
              className="mt-6 flex w-full flex-col items-center gap-2 text-primary"
            >
              <span className="flex size-12 items-center justify-center rounded-xl bg-[#e8f4f8]">
                <Search className="size-5" />
              </span>
              <span className="text-sm font-medium">{content.searchLabel}</span>
            </button>
          </aside>

          <div className="order-2 min-w-0 lg:order-1 lg:col-span-8">
            <div
              dir={isRtl ? "rtl" : "ltr"}
              className="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
              <div className="flex flex-wrap gap-4 border-b border-[#00709E26] sm:gap-6">
                {content.tabs.map((tab) => (
                  <button
                    key={tab.id}
                    type="button"
                    onClick={() => onTabChange(tab.id)}
                    className={cn(
                      "border-b-2 pb-3 text-sm font-bold transition-colors sm:text-base",
                      activeTab === tab.id
                        ? "border-primary text-primary"
                        : "border-transparent text-[#4d5a6f] hover:text-secondary",
                    )}
                  >
                    {tab.label}
                  </button>
                ))}
              </div>

              <div className="inline-flex shrink-0 overflow-hidden rounded-lg border border-[#00709E26] bg-white">
                <button
                  type="button"
                  onClick={() => setViewMode("list")}
                  className={cn(
                    "flex items-center gap-2 px-4 py-2.5 text-sm font-medium transition-colors",
                    viewMode === "list"
                      ? "bg-secondary text-white"
                      : "text-secondary hover:bg-[#f4f6f8]",
                  )}
                >
                  <List className="size-4" />
                  {content.viewListLabel}
                </button>
                <button
                  type="button"
                  onClick={() => setViewMode("map")}
                  className={cn(
                    "flex items-center gap-2 px-4 py-2.5 text-sm font-medium transition-colors",
                    viewMode === "map"
                      ? "bg-secondary text-white"
                      : "text-secondary hover:bg-[#f4f6f8]",
                  )}
                >
                  <MapPin className="size-4" />
                  {content.viewMapLabel}
                </button>
              </div>
            </div>

            <div className="overflow-hidden rounded-2xl bg-white shadow-[1px_1px_18.6px_0px_#111F421C]">
              {viewMode === "list" ? (
                <>
                  <div
                    dir={isRtl ? "rtl" : "ltr"}
                    className="grid bg-primary px-4 py-3 text-sm font-bold text-white sm:px-6 sm:text-base"
                    style={{
                      gridTemplateColumns: isProjects
                        ? "repeat(5, minmax(0, 1fr))"
                        : "120px minmax(0, 1fr) 140px",
                    }}
                  >
                    {isProjects ? (
                      <>
                        <span>{content.columns.projects.number}</span>
                        <span>{content.columns.projects.city}</span>
                        <span>{content.columns.projects.country}</span>
                        <span>{content.columns.projects.startDate}</span>
                        <span>{content.columns.projects.endDate}</span>
                      </>
                    ) : (
                      <>
                        <span>{content.columns.cities.number}</span>
                        <span>{content.columns.cities.name}</span>
                        <span>{content.columns.cities.details}</span>
                      </>
                    )}
                  </div>

                  <div className="max-h-[520px] overflow-y-auto">
                    {rows.map((row, index) => (
                      <div
                        key={`${activeTab}-${row.number}-${index}`}
                        dir={isRtl ? "rtl" : "ltr"}
                        className={cn(
                          "grid items-center gap-3 border-b border-[#00709E14] px-4 py-4 sm:px-6 sm:py-5",
                          index % 2 === 0 ? "bg-[#eef6fa]" : "bg-white",
                        )}
                        style={{
                          gridTemplateColumns: isProjects
                            ? "repeat(5, minmax(0, 1fr))"
                            : "120px minmax(0, 1fr) 140px",
                        }}
                      >
                        {isProjects && "city" in row ? (
                          <>
                            <span className="text-sm font-bold text-secondary sm:text-base">
                              #{row.number}
                            </span>
                            <span className="text-sm text-secondary sm:text-base">
                              {row.city}
                            </span>
                            <span className="text-sm text-secondary sm:text-base">
                              {row.country}
                            </span>
                            <span className="text-sm text-secondary sm:text-base">
                              {row.startDate}
                            </span>
                            <span className="text-sm text-secondary sm:text-base">
                              {row.endDate}
                            </span>
                          </>
                        ) : "name" in row ? (
                          <>
                            <span className="text-sm font-bold text-secondary sm:text-base">
                              #{row.number}
                            </span>
                            <div>
                              <p className="text-sm font-bold text-secondary sm:text-base">
                                {row.name}
                              </p>
                              {row.description ? (
                                <p className="mt-1 text-xs leading-6 text-[#4d5a6f] sm:text-sm">
                                  {row.description}
                                </p>
                              ) : null}
                            </div>
                            <button
                              type="button"
                              className="rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white transition-colors hover:bg-primary/90 sm:px-5 sm:text-sm"
                            >
                              {content.seeMoreLabel}
                            </button>
                          </>
                        ) : null}
                      </div>
                    ))}
                  </div>
                </>
              ) : (
                <div className="flex min-h-[320px] items-center justify-center p-8 text-center text-sm text-[#4d5a6f] sm:text-base">
                  {content.mapPlaceholder}
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
