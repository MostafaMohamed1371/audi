"use client";

import { EmployeePortrait } from "@/app/components/ui/employee-portrait";
import { MemberProfileDialog } from "@/app/components/ui/member-profile-dialog";
import { cn } from "@/lib/utils";
import { ChevronLeft } from "lucide-react";
import { useState } from "react";

export type AdvisoryBoardMember = {
  id: string;
  featured?: boolean;
  role: string;
  name: string;
  image: string;
  bio: string;
};

type Props = {
  members: AdvisoryBoardMember[];
  readMore: string;
  pageTitle: string;
  isRtl?: boolean;
};

export function AdvisoryBoardSection({
  members,
  readMore,
  pageTitle,
  isRtl = true,
}: Props) {
  const [activeId, setActiveId] = useState<string | null>(null);

  const featured = members.find((member) => member.featured);
  const gridMembers = members.filter((member) => !member.featured);
  const activeMember = members.find((member) => member.id === activeId) ?? null;

  return (
    <section className="bg-white py-16 sm:py-20 lg:py-24">
      <div className="mx-auto max-w-7xl px-4 sm:px-6">
        {featured ? (
          <FeaturedMemberCard
            member={featured}
            readMore={readMore}
            isRtl={isRtl}
            onReadMore={() => setActiveId(featured.id)}
          />
        ) : null}

        <div className="mt-8 grid gap-6 sm:grid-cols-2 lg:mt-10 lg:grid-cols-3 lg:gap-8">
          {gridMembers.map((member) => (
            <MemberCard
              key={member.id}
              member={member}
              readMore={readMore}
              isRtl={isRtl}
              onReadMore={() => setActiveId(member.id)}
            />
          ))}
        </div>
      </div>

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
    </section>
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

function FeaturedMemberCard({
  member,
  readMore,
  isRtl,
  onReadMore,
}: {
  member: AdvisoryBoardMember;
  readMore: string;
  isRtl: boolean;
  onReadMore: () => void;
}) {
  return (
    <article className="rounded-2xl bg-white p-6 shadow-[1px_1px_18.6px_0px_#111F421C] sm:p-8">
      <div
        dir="ltr"
        className="flex flex-col items-stretch gap-8 lg:flex-row lg:items-center lg:gap-12"
      >
        <div
          dir={isRtl ? "rtl" : "ltr"}
          className="order-2 flex flex-1 flex-col gap-4 text-start lg:order-1"
        >
          <p className="text-base text-secondary sm:text-lg">{member.role}</p>
          <h2 className="text-2xl font-bold leading-snug text-primary sm:text-3xl">
            {member.name}
          </h2>
          <ReadMoreButton
            label={readMore}
            isRtl={isRtl}
            onClick={onReadMore}
            className="mt-2 self-start"
          />
        </div>

        <div className="order-1 mx-auto w-full max-w-[320px] shrink-0 rounded-xl border-2 border-primary/15 p-3 lg:order-2 lg:mx-0 lg:max-w-[360px]">
          <EmployeePortrait
            image={member.image}
            alt={member.name}
            priority
            sizes="(max-width: 1024px) 320px, 360px"
          />
        </div>
      </div>
    </article>
  );
}

function MemberCard({
  member,
  readMore,
  isRtl,
  onReadMore,
}: {
  member: AdvisoryBoardMember;
  readMore: string;
  isRtl: boolean;
  onReadMore: () => void;
}) {
  return (
    <article
      dir={isRtl ? "rtl" : "ltr"}
      className="flex flex-col overflow-hidden rounded-2xl bg-white shadow-[1px_1px_18.6px_0px_#111F421C]"
    >
      <div className="px-6 pt-6">
        <EmployeePortrait
          image={member.image}
          alt={member.name}
          className="mx-auto max-w-[240px]"
          sizes="(max-width: 640px) 50vw, 240px"
        />
      </div>

      <div className="flex flex-1 flex-col gap-3 p-6 pt-4 text-start">
        <p className="text-sm text-secondary sm:text-base">{member.role}</p>
        <h3 className="text-lg font-bold leading-snug text-primary sm:text-xl">
          {member.name}
        </h3>
        <ReadMoreButton
          label={readMore}
          isRtl={isRtl}
          onClick={onReadMore}
          className="mt-auto pt-2"
        />
      </div>
    </article>
  );
}
