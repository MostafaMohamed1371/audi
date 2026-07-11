import { PanelWrapper } from "@/app/components/programs/training/shared/panel-wrapper";
import { UrbanPoliciesProjects } from "@/app/components/programs/urban-policies/sections/urban-policies-projects";
import type { UrbanPoliciesReportsContent } from "@/app/components/programs/urban-policies/shared/types";
import type { PartnershipsPanelProps } from "@/app/components/programs/partnerships/shared/types";

type Props = PartnershipsPanelProps & {
  content: UrbanPoliciesReportsContent;
};

export function UrbanPoliciesReportsPanel({
  content,
  isRtl,
  backLabel,
  onBack,
}: Props) {
  const projects = content.projects ?? [];
  const hasProjects = projects.length > 0;

  return (
    <PanelWrapper backLabel={backLabel} onBack={onBack} isRtl={isRtl}>
      <div dir={isRtl ? "rtl" : "ltr"} className="space-y-10 sm:space-y-12 lg:space-y-16">
        {content.intro ? (
          <p className="mx-auto max-w-4xl text-center text-base leading-8 text-muted-foreground sm:text-lg sm:leading-9">
            {content.intro}
          </p>
        ) : null}

        {content.video ? (
          <div className="mx-auto max-w-5xl overflow-hidden rounded-2xl bg-white shadow-[0_8px_32px_rgba(17,31,66,0.08)]">
            <video
              className="aspect-video w-full object-cover"
              controls
              playsInline
              preload="metadata"
              poster={content.videoPoster}
            >
              <source src={content.video} type="video/mp4" />
            </video>
          </div>
        ) : null}

        {hasProjects ? (
          <div className="space-y-8 sm:space-y-10">
            {content.projectsTitle ? (
              <h2 className="text-center text-2xl font-bold text-secondary sm:text-3xl lg:text-4xl">
                {content.projectsTitle}
              </h2>
            ) : null}

            <UrbanPoliciesProjects
              items={projects}
              viewIssue={content.viewIssue ?? ""}
              isRtl={isRtl}
            />
          </div>
        ) : null}
      </div>
    </PanelWrapper>
  );
}
