"use client";

import type { PartnershipsTab } from "@/lib/programs-partnerships";
import { PartnershipSectionPanel } from "@/app/components/programs/partnerships/sections/section-panel";
import type { PartnershipSectionContent } from "@/app/components/programs/partnerships/shared/types";

type Props = {
  activeTab: PartnershipsTab;
  isRtl: boolean;
  backLabel: string;
  onBack: () => void;
  euroArabDialogue: PartnershipSectionContent;
  secretarySpeaks: PartnershipSectionContent;
  urbanAwards: PartnershipSectionContent;
  partnersGuide: PartnershipSectionContent;
};

export function PartnershipsTabPanels({
  activeTab,
  isRtl,
  backLabel,
  onBack,
  euroArabDialogue,
  secretarySpeaks,
  urbanAwards,
  partnersGuide,
}: Props) {
  const panelProps = { isRtl, backLabel, onBack };

  switch (activeTab) {
    case "euroArabDialogue":
      return (
        <PartnershipSectionPanel
          content={euroArabDialogue}
          {...panelProps}
        />
      );
    case "secretarySpeaks":
      return (
        <PartnershipSectionPanel content={secretarySpeaks} {...panelProps} />
      );
    case "urbanAwards":
      return (
        <PartnershipSectionPanel content={urbanAwards} {...panelProps} />
      );
    case "partnersGuide":
      return (
        <PartnershipSectionPanel content={partnersGuide} {...panelProps} />
      );
    default:
      return null;
  }
}
