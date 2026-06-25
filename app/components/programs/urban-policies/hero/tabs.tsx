"use client";

import { ProgramHeroTabs } from "@/app/components/programs/program-hero-tabs";
import type { UrbanPoliciesTab } from "@/lib/programs-urban-policies";

type Tab = {
  id: UrbanPoliciesTab;
  label: string;
  previewImage?: string;
};

type Props = {
  tabs: Tab[];
  activeTab: UrbanPoliciesTab;
  onTabChange: (tab: UrbanPoliciesTab) => void;
  variant?: "hero" | "content";
};

export function UrbanPoliciesHeroTabs({
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
      ariaLabel="Urban policies program tabs"
    />
  );
}
