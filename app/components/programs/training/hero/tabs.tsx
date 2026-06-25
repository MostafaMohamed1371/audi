"use client";

import { ProgramHeroTabs } from "@/app/components/programs/program-hero-tabs";
import type { TrainingTab } from "@/lib/programs-training";

type Tab = {
  id: TrainingTab;
  label: string;
  video?: string;
};

type Props = {
  tabs: Tab[];
  activeTab: TrainingTab;
  onTabChange: (tab: TrainingTab) => void;
  variant?: "hero" | "content";
};

export function TrainingHeroTabs({
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
      ariaLabel="Training program tabs"
    />
  );
}
