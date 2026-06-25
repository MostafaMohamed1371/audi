"use client";

import { Link, type AppHref } from "@/i18n/routing";
import { cn } from "@/lib/utils";
import { useLocale } from "next-intl";

type CardContent = {
  title: string;
  description: string;
  readMore: string;
};

type VisionMissionCardsProps = {
  mission: CardContent;
  vision: CardContent;
};

export function VisionMissionCards({ mission, vision }: VisionMissionCardsProps) {
  return (
    <div className="grid gap-4 px-0 sm:gap-6 sm:px-8 md:grid-cols-2 md:gap-8">
      <IntroCard {...mission} href="/about" />
      <IntroCard {...vision} href="/about" />
    </div>
  );
}

function IntroCard({
  title,
  description,
  readMore,
  href,
}: CardContent & { href: AppHref }) {
  const locale = useLocale();
  const isRtl = locale === "ar";

  return (
    <article
      dir={isRtl ? "rtl" : "ltr"}
      className={cn(
        "group relative flex min-h-[240px] flex-col rounded-2xl bg-white p-6 sm:min-h-[300px] sm:p-8 lg:p-10",
        "shadow-[0_12px_40px_rgba(0,112,158,0.08)] transition-all duration-300",
        "hover:bg-primary hover:shadow-[0_16px_48px_rgba(0,112,158,0.18)]"
      )}
    >
      <h3 className="mb-5 text-start text-xl font-bold text-secondary group-hover:text-white sm:text-[1.35rem]">
        <span className="inline-flex items-center gap-3">
          <span className="size-2.5 shrink-0 rounded-sm bg-primary transition-colors group-hover:bg-white" />
          {title}
        </span>
      </h3>

      <p className="pb-10 text-start text-[0.95rem] leading-8 text-muted-foreground transition-colors group-hover:text-white/90">
        {description}
      </p>

      <Link
        href={href}
        className="absolute bottom-8 start-8 text-sm font-medium text-primary transition-colors group-hover:text-white"
      >
        {readMore}
      </Link>
    </article>
  );
}
