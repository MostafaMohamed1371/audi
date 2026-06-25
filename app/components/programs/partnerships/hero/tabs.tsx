"use client";

import { ProgramHeroTabs } from "@/app/components/programs/program-hero-tabs";
import type { PartnershipsTab } from "@/lib/programs-partnerships";

type Tab = {
  id: PartnershipsTab;
  label: string;
  previewImage?: string;
};

type Props = {
  tabs: Tab[];
  activeTab: PartnershipsTab;
  onTabChange: (tab: PartnershipsTab) => void;
  variant?: "hero" | "content";
};

export function PartnershipsHeroTabs({
  tabs,
  activeTab,
  onTabChange,
  variant = "hero",
}: Props) {
  if (variant !== "hero") {
    return null;
  }

  return (
    <ProgramHeroTabs
      tabs={tabs}
      activeTab={activeTab}
      onTabChange={onTabChange}
      ariaLabel="Partnerships program tabs"
    />
  );
}
