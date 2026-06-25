"use client";

import { PanelWrapper } from "@/app/components/programs/training/shared/panel-wrapper";
import { DevelopmentPortalDirectory } from "@/app/components/programs/urban-policies/sections/development-portal-directory";
import { PortalContributionForm } from "@/app/components/programs/urban-policies/sections/portal-contribution-form";
import type { DevelopmentPortalContent } from "@/app/components/programs/urban-policies/shared/types";
import {
  isPortalDirectoryTab,
  type PortalDirectoryTab,
} from "@/lib/programs-urban-policies-directory";
import { Mail } from "lucide-react";
import { useTranslations } from "next-intl";
import { useRouter as useNextRouter, useSearchParams } from "next/navigation";
import { useCallback, useEffect, useRef, useState } from "react";

type Props = {
  content: DevelopmentPortalContent;
  isRtl: boolean;
  backLabel: string;
  onBack: () => void;
};

export function DevelopmentPortalPanel({
  content,
  isRtl,
  backLabel,
  onBack,
}: Props) {
  const contributionFormT = useTranslations(
    "programs.urbanPolicies.developmentPortal.contributionForm",
  );
  const nextRouter = useNextRouter();
  const searchParams = useSearchParams();
  const directoryRef = useRef<HTMLDivElement>(null);
  const [showContributionForm, setShowContributionForm] = useState(false);

  const directoryParam = searchParams.get("directory");
  const activeDirectoryTab =
    directoryParam && isPortalDirectoryTab(directoryParam)
      ? directoryParam
      : null;

  const openDirectory = useCallback(
    (id: PortalDirectoryTab) => {
      const params = new URLSearchParams(searchParams.toString());
      params.set("tab", "developmentPortal");
      params.set("directory", id);
      nextRouter.push(`?${params.toString()}`, { scroll: false });
    },
    [nextRouter, searchParams],
  );

  const setDirectoryTab = useCallback(
    (tab: PortalDirectoryTab) => {
      const params = new URLSearchParams(searchParams.toString());
      params.set("directory", tab);
      nextRouter.push(`?${params.toString()}`, { scroll: false });
    },
    [nextRouter, searchParams],
  );

  useEffect(() => {
    if (activeDirectoryTab && directoryRef.current) {
      directoryRef.current.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  }, [activeDirectoryTab]);

  return (
    <>
      <PanelWrapper backLabel={backLabel} onBack={onBack} isRtl={isRtl}>
        <div
          dir={isRtl ? "rtl" : "ltr"}
          className="grid items-start gap-10 lg:grid-cols-12 lg:gap-12 xl:gap-16"
        >
          <div className="flex items-center justify-center lg:col-span-4 lg:min-h-[420px]">
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img
              src={content.image}
              alt=""
              className="h-auto w-full object-contain"
            />
          </div>

          <div
            dir={isRtl ? "rtl" : "ltr"}
            className="space-y-8 text-start lg:col-span-8"
          >
            <h2 className="text-[1.75rem] font-bold leading-tight text-secondary sm:text-4xl lg:text-[40px]">
              {content.title}
            </h2>

            <div className="space-y-5 text-base leading-8 text-[#4d5a6f] sm:text-lg sm:leading-9">
              {content.paragraphs.map((paragraph) => (
                <p key={paragraph}>{paragraph}</p>
              ))}
            </div>

            <div className="space-y-4">
              <h3 className="text-xl font-bold text-secondary sm:text-2xl">
                {content.contributeTitle}
              </h3>
              <p className="text-base leading-8 text-[#4d5a6f] sm:text-lg sm:leading-9">
                {content.contributeDescription}
              </p>
            </div>

            <a
              href={`mailto:${content.email}`}
              dir="ltr"
              className="inline-flex w-full max-w-[520px] items-center justify-between gap-4 rounded-[10px] border border-[#00709E33] bg-[#e8f4f8] px-5 py-4 text-primary transition-colors hover:bg-[#d9edf4]"
            >
              <span className="text-base font-medium sm:text-lg">
                {content.email}
              </span>
              <Mail className="size-5 shrink-0" strokeWidth={1.5} />
            </a>

            {showContributionForm ? (
              <PortalContributionForm
                isRtl={isRtl}
                onSuccess={() => setShowContributionForm(false)}
              />
            ) : (
              <button
                type="button"
                onClick={() => setShowContributionForm(true)}
                className="text-sm font-medium text-primary underline-offset-4 hover:underline"
              >
                {contributionFormT("openLabel")}
              </button>
            )}
          </div>
        </div>
      </PanelWrapper>

      <section
        dir={isRtl ? "rtl" : "ltr"}
        className="mt-10 w-full bg-primary py-8 sm:mt-12 sm:py-10"
      >
        <div className="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:gap-10">
          <div className="max-w-[640px] space-y-3 text-start">
            <h3 className="text-2xl font-bold text-white sm:text-[28px]">
              {content.ctaTitle}
            </h3>
            <p className="text-sm leading-7 text-white/85 sm:text-base sm:leading-8">
              {content.ctaDisclaimer}
            </p>
          </div>

          <div className="flex flex-wrap justify-center gap-3 sm:justify-start sm:gap-4 lg:justify-end">
            {content.contributionTypes.map((type) => (
              <button
                key={type.id}
                type="button"
                onClick={() => {
                  if (isPortalDirectoryTab(type.id)) {
                    openDirectory(type.id);
                  }
                }}
                className="min-h-[52px] min-w-[140px] flex-1 rounded-xl bg-white px-8 py-3.5 text-center text-sm font-bold text-primary transition-colors hover:bg-white/90 sm:min-w-[160px] sm:flex-none sm:text-base"
              >
                {type.label}
              </button>
            ))}
          </div>
        </div>
      </section>

      {activeDirectoryTab ? (
        <div ref={directoryRef}>
          <DevelopmentPortalDirectory
            content={content.directory}
            activeTab={activeDirectoryTab}
            isRtl={isRtl}
            onTabChange={setDirectoryTab}
          />
        </div>
      ) : null}
    </>
  );
}
