"use client";

import { EmployeePortrait } from "@/app/components/ui/employee-portrait";
import { HighlightedTitle } from "@/app/components/ui/highlighted-title";
import { MemberProfileDialog } from "@/app/components/ui/member-profile-dialog";
import { cn } from "@/lib/utils";
import { ChevronLeft } from "lucide-react";
import { useState } from "react";

const CARD_SHADOW = "shadow-[1px_1px_18.6px_0px_#111F421C]";

export type TeamMember = {
  id: string;
  role: string;
  name: string;
  image: string;
  bio: string;
};

export type TeamSection = {
  id: string;
  title: string;
  members: TeamMember[];
};

type Props = {
  sections: TeamSection[];
  readMore: string;
  pageTitle: string;
  isRtl?: boolean;
};

export function TeamSection({
  sections,
  readMore,
  pageTitle,
  isRtl = true,
}: Props) {
  const [activeId, setActiveId] = useState<string | null>(null);

  const allMembers = sections.flatMap((section) => section.members);
  const activeMember = allMembers.find((member) => member.id === activeId) ?? null;

  return (
    <>
      <section className="bg-white py-16 sm:py-20 lg:py-24">
        <div className="mx-auto max-w-7xl space-y-12 px-4 sm:space-y-16 sm:px-6 lg:space-y-20">
          {sections.map((section, index) => (
            <TeamCategoryBlock
              key={section.id}
              section={section}
              readMore={readMore}
              isRtl={isRtl}
              onReadMore={setActiveId}
              showDivider={index > 0}
            />
          ))}
        </div>
      </section>

      {activeMember ? (
        <MemberProfileDialog
          open
          onOpenChange={(open) => {
            if (!open) setActiveId(null);
          }}
          pageTitle={pageTitle}
          name={activeMember.name}
          role={activeMember.role}
          image={activeMember.image}
          bio={activeMember.bio}
          isRtl={isRtl}
        />
      ) : null}
    </>
  );
}

function ReadMoreButton({
  label,
  isRtl,
  onClick,
  className,
}: {
  label: string;
  isRtl: boolean;
  onClick: () => void;
  className?: string;
}) {
  return (
    <button
      type="button"
      onClick={onClick}
      className={cn(
        "inline-flex items-center gap-1.5 text-sm font-medium text-primary transition-colors hover:text-primary/80",
        className,
      )}
    >
      <span>{label}</span>
      <ChevronLeft
        className={cn("size-4", !isRtl && "rotate-180")}
        aria-hidden
      />
    </button>
  );
}

function TeamCategoryBlock({
  section,
  readMore,
  isRtl,
  onReadMore,
  showDivider,
}: {
  section: TeamSection;
  readMore: string;
  isRtl: boolean;
  onReadMore: (id: string) => void;
  showDivider: boolean;
}) {
  const isSingle = section.members.length === 1;

  return (
    <div
      className={cn(
        showDivider && "border-t border-border/30 pt-12 sm:pt-16 lg:pt-20",
      )}
    >
      <div
        dir={isRtl ? "rtl" : "ltr"}
        className="flex flex-col gap-8 lg:flex-row lg:items-start lg:gap-10 xl:gap-14"
      >
        <h2 className="shrink-0 text-center text-2xl font-bold text-secondary sm:text-3xl lg:min-w-[8rem] lg:text-start">
          {section.title}
        </h2>

        <div
          dir={isRtl ? "rtl" : "ltr"}
          className={cn(
            "grid flex-1 items-stretch gap-6 sm:gap-8",
            isSingle
              ? "max-w-[280px] justify-items-center"
              : "sm:grid-cols-2 lg:grid-cols-3",
          )}
        >
          {section.members.map((member) => (
            <TeamMemberCard
              key={member.id}
              member={member}
              readMore={readMore}
              isRtl={isRtl}
              onReadMore={() => onReadMore(member.id)}
            />
          ))}
        </div>
      </div>
    </div>
  );
}

function TeamMemberCard({
  member,
  readMore,
  isRtl,
  onReadMore,
}: {
  member: TeamMember;
  readMore: string;
  isRtl: boolean;
  onReadMore: () => void;
}) {
  return (
    <article
      dir={isRtl ? "rtl" : "ltr"}
      className={cn(
        "flex h-full flex-col overflow-hidden rounded-2xl bg-white",
        CARD_SHADOW,
      )}
    >
      <div className="flex justify-center px-6 pt-6">
        <div className="w-full max-w-[220px]">
          <EmployeePortrait
            image={member.image}
            alt={member.name}
            className="w-full"
            sizes="220px"
          />
        </div>
      </div>

      <div className="flex flex-1 flex-col items-center p-6 pt-4 text-center">
        <p className="text-sm leading-6 text-secondary line-clamp-2 sm:text-base sm:leading-7">
          {member.role}
        </p>
        <div className="mt-3">
          <HighlightedTitle
            as="h3"
            title={member.name}
            className="text-lg leading-snug sm:text-xl"
          />
        </div>

        <ReadMoreButton
          label={readMore}
          isRtl={isRtl}
          onClick={onReadMore}
          className="mt-auto pt-4"
        />
      </div>
    </article>
  );
}
