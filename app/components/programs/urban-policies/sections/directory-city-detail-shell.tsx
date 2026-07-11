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

export function DirectoryCityDetailShell({
  number,
  isRtl,
  directoryUi,
  initialData,
}: Props) {
  const router = useRouter();

  const onBack = () => {
    router.push({
      pathname: "/programs/urban-policies",
      query: { tab: "developmentPortal", directory: "cities" },
    });
  };

  return (
    <div className="min-h-dvh bg-white pt-20">
      <DirectoryItemDetailPanel
        tab="cities"
        number={number}
        isRtl={isRtl}
        initialData={initialData}
        fallbackUi={{
          discussionTitle: directoryUi.discussionTitle,
          addCommentLabel: directoryUi.addCommentLabel,
          authorNameLabel: directoryUi.authorNameLabel,
          commentBodyLabel: directoryUi.commentBodyLabel,
          submitCommentLabel: directoryUi.submitCommentLabel,
          backToListLabel: directoryUi.backToListLabel,
          commentSuccess: directoryUi.commentSuccess,
          commentError: directoryUi.commentError,
          shareLabel: directoryUi.shareLabel,
          downloadLabel: directoryUi.downloadLabel,
          addressLabel: directoryUi.addressLabel,
          sourceLabel: directoryUi.sourceLabel,
          relatedProjectsTitle: directoryUi.relatedProjectsTitle,
        }}
        onBack={onBack}
      />
    </div>
  );
}
