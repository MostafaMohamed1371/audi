"use client";

import { DirectoryItemDetailPanel } from "@/app/components/programs/urban-policies/sections/directory-item-detail";
import type { DevelopmentPortalContent } from "@/app/components/programs/urban-policies/shared/types";
import type { DirectoryItemDetail } from "@/lib/api";
import { useRouter } from "@/i18n/routing";

type Props = {
  number: string;
  isRtl: boolean;
  directoryUi: DevelopmentPortalContent["directory"];
  initialData: DirectoryItemDetail | null;
};

export function DirectoryProjectDetailShell({
  number,
  isRtl,
  directoryUi,
  initialData,
}: Props) {
  const router = useRouter();

  const onBack = () => {
    router.push({
      pathname: "/programs/urban-policies",
      query: { tab: "developmentPortal", directory: "projects" },
    });
  };

  return (
    <div className="min-h-dvh bg-white pt-20">
      <DirectoryItemDetailPanel
        tab="projects"
        number={number}
        isRtl={isRtl}
        initialData={initialData}
        fallbackUi={{
          backToListLabel: directoryUi.backToListLabel,
          shareLabel: directoryUi.shareLabel,
          downloadLabel: directoryUi.downloadLabel,
          relatedProjectsTitle: directoryUi.relatedProjectsTitle,
          projectDescriptionTitle: directoryUi.projectDescriptionTitle,
          projectValuesTitle: directoryUi.projectValuesTitle,
          projectPolicyToolsTitle: directoryUi.projectPolicyToolsTitle,
          viewSourceLabel: directoryUi.viewSourceLabel,
          foundersTitle: directoryUi.foundersTitle,
          referencesAccordionTitle: directoryUi.referencesAccordionTitle,
          projectLinkLabel: directoryUi.projectLinkLabel,
          notesLabel: directoryUi.notesLabel,
          referencesLabel: directoryUi.referencesLabel,
          directoryCta: directoryUi.cta,
        }}
        onBack={onBack}
      />
    </div>
  );
}
