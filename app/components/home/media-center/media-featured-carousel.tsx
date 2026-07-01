"use client";

import Image from "next/image";
import { Link, type AppHref } from "@/i18n/routing";
import { ButtonLink } from "@/app/components/ui/button";
import { ChevronLeft, ChevronRight } from "lucide-react";
import { useCallback, useState } from "react";
import { cn } from "@/lib/utils";

type Slide = {
  title: string;
  description: string;
  date: string;
  href: AppHref;
  image: string;
};

type Props = {
  slides: Slide[];
  readMore: string;
  isRtl: boolean;
};

function DiamondIcon() {
  return (
    <svg
      width="18"
      height="18"
      viewBox="0 0 18 18"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      aria-hidden
      className="mb-4 text-white/90"
    >
      <rect
        x="9"
        y="1.5"
        width="10.6"
        height="10.6"
        rx="1"
        transform="rotate(45 9 1.5)"
        stroke="currentColor"
        strokeWidth="1.2"
      />
    </svg>
  );
}

export function MediaFeaturedCarousel({ slides, readMore, isRtl }: Props) {
  const [current, setCurrent] = useState(0);
  const total = slides.length;
  const canNavigate = total > 1;

  const goTo = useCallback(
    (index: number) => {
      if (!canNavigate) return;
      setCurrent((index + total) % total);
    },
    [canNavigate, total],
  );

  const goNext = useCallback(() => goTo(current + 1), [current, goTo]);
  const goPrev = useCallback(() => goTo(current - 1), [current, goTo]);

  if (total === 0) return null;

  const item = slides[current]!;

  return (
    <div>
      <div className="grid gap-6 sm:gap-8 lg:grid-cols-2 lg:items-center lg:gap-12">
        <div className="relative order-1 aspect-[16/10] overflow-hidden rounded-2xl sm:aspect-[4/3] sm:rounded-[20px] lg:order-1 lg:aspect-auto lg:h-[320px] xl:h-[360px]">
          <Image
            key={item.image}
            src={item.image}
            alt={item.title}
            fill
            className="object-cover transition-opacity duration-500"
            sizes="(max-width: 1024px) 100vw, 50vw"
            priority={current === 0}
          />
        </div>

        <div
          className={`order-2 flex flex-col ${isRtl ? "items-start text-right" : "items-start text-left"}`}
        >
          <DiamondIcon />

          <h3
            key={`${item.title}-title`}
            className="mb-3 text-lg font-bold leading-snug text-white sm:mb-4 sm:text-2xl lg:text-[1.65rem]"
          >
            {item.title}
          </h3>

          <p
            key={`${item.title}-desc`}
            className="mb-5 text-sm leading-7 text-white/75 sm:mb-6 sm:text-[0.95rem] sm:leading-8"
          >
            {item.description}
          </p>

          <time className="mb-6 block text-xs font-medium tracking-widest text-white/55 uppercase">
            {item.date}
          </time>

          <ButtonLink
            size="sm"
            className="rounded-full bg-primary px-5 hover:bg-primary/90"
            render={<Link href={item.href} />}
          >
            {readMore}
            <ChevronLeft className="size-3.5" />
          </ButtonLink>
        </div>
      </div>

      {canNavigate ? (
        <div
          className={cn(
            "mt-6 flex items-center gap-4 sm:mt-8",
            isRtl ? "flex-row-reverse" : "flex-row",
          )}
        >
          <div className="flex shrink-0 gap-3">
            <button
              type="button"
              onClick={goPrev}
              aria-label={isRtl ? "السابق" : "Previous"}
              className="flex size-10 items-center justify-center rounded-full bg-primary text-white transition-colors hover:bg-primary/90 cursor-pointer"
            >
            <ChevronLeft className="size-4 rtl:rotate-180" />
            </button>
            <button
              type="button"
              onClick={goNext}
              aria-label={isRtl ? "التالي" : "Next"}
              className="flex size-10 items-center justify-center rounded-full bg-primary text-white transition-colors hover:bg-primary/90 cursor-pointer"
            >
              <ChevronRight className="size-4 rtl:rotate-180" />
            </button>
          </div>
          <div className="h-px flex-1 bg-white/25" aria-hidden />
        </div>
      ) : null}
    </div>
  );
}