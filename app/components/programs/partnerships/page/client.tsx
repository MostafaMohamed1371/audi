"use client";

import { MediaHeroVideo } from "@/app/components/media/media-hero-video";
import { useCallback, useMemo } from "react";
import { useRouter } from "@/i18n/routing";
import { useSearchParams, useRouter as useNextRouter } from "next/navigation";
import { PartnershipsHeroTabs } from "@/app/components/programs/partnerships/hero/tabs";
import { PartnershipsTabPanels } from "@/app/components/programs/partnerships/sections";
import {
  isPartnershipsTab,
  type PartnershipsTab,
} from "@/lib/programs-partnerships";
import type { PartnershipSectionContent } from "@/app/components/programs/partnerships/shared/types";

type Tab = {
  id: PartnershipsTab;
  label: string;
  previewImage?: string;
};

type HeroContent = {
  programLabel: string;
  title: string;
  intro: string;
  sectionsLabel: string;
  video: string;
  overlay: string;
};

type Props = {
  tabs: Tab[];
  defaultTab: PartnershipsTab;
  isRtl: boolean;
  hero: HeroContent;
  backLabel: string;
  euroArabDialogue: PartnershipSectionContent;
  secretarySpeaks: PartnershipSectionContent;
  urbanAwards: PartnershipSectionContent;
  partnersGuide: PartnershipSectionContent;
};

export function PartnershipsPageClient({
  tabs,
  defaultTab,
  isRtl,
  hero,
  backLabel,
  euroArabDialogue,
  secretarySpeaks,
  urbanAwards,
  partnersGuide,
}: Props) {
  const router = useRouter();
  const nextRouter = useNextRouter();
  const searchParams = useSearchParams();

  const activeTab = useMemo(() => {
    const tabParam = searchParams.get("tab");
    return tabParam && isPartnershipsTab(tabParam) ? tabParam : null;
  }, [searchParams]);

  const onTabChange = useCallback(
    (tab: PartnershipsTab) => {
      const params = new URLSearchParams(searchParams.toString());
      params.set("tab", tab);
      nextRouter.push(`?${params.toString()}`, { scroll: true });
    },
    [nextRouter, searchParams],
  );

  const onBack = useCallback(() => {
    router.push("/programs/partnerships", { scroll: true });
  }, [router]);

  if (activeTab) {
    return (
      <div className="bg-background">
        <div className="py-10 sm:py-12 lg:py-16 pb-0!">
          <PartnershipsTabPanels
            activeTab={activeTab}
            isRtl={isRtl}
            backLabel={backLabel}
            onBack={onBack}
            euroArabDialogue={euroArabDialogue}
            secretarySpeaks={secretarySpeaks}
            urbanAwards={urbanAwards}
            partnersGuide={partnersGuide}
          />
        </div>
      </div>
    );
  }

  return (
    <div className="bg-background">
      <section className="relative min-h-dvh lg:h-screen lg:overflow-hidden">
        <div className="relative min-h-dvh lg:h-full">
          <MediaHeroVideo src={hero.video} />
          <div
            className="absolute inset-0"
            style={{ background: hero.overlay }}
            aria-hidden
          />

          <div className="relative z-10 flex min-h-dvh flex-col lg:h-full">
            <div className="mx-auto flex w-full max-w-7xl flex-1 items-center px-4 pt-24 pb-8 sm:px-6 sm:pt-28 sm:pb-10 lg:pt-36 lg:pb-0">
              <div
                dir={isRtl ? "rtl" : "ltr"}
                className="w-full max-w-3xl space-y-2 text-start sm:space-y-3 lg:max-w-[720px] lg:space-y-4"
              >
                <p className="text-base font-semibold text-white sm:text-lg lg:text-xl">
                  {hero.programLabel}
                </p>
                <h1 className="text-2xl font-bold leading-tight text-white sm:text-3xl lg:text-[44px]">
                  {hero.title}
                </h1>
                <p className="max-w-2xl text-sm leading-7 text-white/90 sm:text-base sm:leading-8 lg:text-lg lg:leading-9">
                  {hero.intro}
                </p>
              </div>
            </div>

            <div
              dir={isRtl ? "rtl" : "ltr"}
              className="mx-auto w-full max-w-7xl shrink-0 px-4 pb-5 sm:px-6 sm:pb-6 lg:pb-6"
            >
              <h2 className="mb-3 text-start text-lg font-bold text-white sm:mb-5 sm:text-xl lg:mb-6 lg:text-2xl">
                {hero.sectionsLabel}
              </h2>
              <PartnershipsHeroTabs
                tabs={tabs}
                activeTab={defaultTab}
                onTabChange={onTabChange}
                variant="hero"
              />
            </div>

            <div className="h-px w-full shrink-0 bg-white/50" aria-hidden />
          </div>
        </div>
      </section>
    </div>
  );
}
