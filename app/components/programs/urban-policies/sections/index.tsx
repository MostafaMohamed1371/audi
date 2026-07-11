"use client";

import type { UrbanPoliciesTab } from "@/lib/programs-urban-policies";
import { PartnershipSectionPanel } from "@/app/components/programs/partnerships/sections/section-panel";
import { DevelopmentPortalPanel } from "@/app/components/programs/urban-policies/sections/development-portal-panel";
import { UrbanPoliciesReportsPanel } from "@/app/components/programs/urban-policies/sections/reports-panel";
import type {
  DevelopmentPortalContent,
  UrbanPoliciesReportsContent,
  UrbanPoliciesSectionContent,
} from "@/app/components/programs/urban-policies/shared/types";

type Props = {
  activeTab: UrbanPoliciesTab;
  isRtl: boolean;
  backLabel: string;
  onBack: () => void;
  developmentPortal: DevelopmentPortalContent;
  developmentIndex: UrbanPoliciesSectionContent;
  innovationLab: UrbanPoliciesReportsContent;
  practiceReports: UrbanPoliciesReportsContent;
};

export function UrbanPoliciesTabPanels({
  activeTab,
  isRtl,
  backLabel,
  onBack,
  developmentPortal,
  developmentIndex,
  innovationLab,
  practiceReports,
}: Props) {
  const panelProps = { isRtl, backLabel, onBack };

  switch (activeTab) {
    case "developmentPortal":
      return (
        <DevelopmentPortalPanel content={developmentPortal} {...panelProps} />
      );
    case "developmentIndex":
      return (
        <PartnershipSectionPanel content={developmentIndex} {...panelProps} />
      );
    case "innovationLab":
      return (
        <UrbanPoliciesReportsPanel content={innovationLab} {...panelProps} />
      );
    case "practiceReports":
      return (
        <UrbanPoliciesReportsPanel content={practiceReports} {...panelProps} />
      );
    default:
      return null;
  }
}
