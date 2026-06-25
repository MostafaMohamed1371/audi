"use client";

import Image from "next/image";
import { ChevronLeft, ChevronRight } from "lucide-react";
import { useCallback, useState } from "react";
import { cn } from "@/lib/utils";
import { useVisibleIndices } from "@/app/components/programs/training/sections/experts/use-visible-indices";
import { PanelWrapper } from "@/app/components/programs/training/shared/panel-wrapper";
import type {
  ExpertsContent,
  TrainingPanelProps,
} from "@/app/components/programs/training/shared/types";

type Props = TrainingPanelProps & {
  content: ExpertsContent;
};

export function ExpertsPanel({
  content,
  isRtl,
  backLabel,
  onBack,
}: Props) {
  const [current, setCurrent] = useState(0);
  const total = content.experts.length;

  const goTo = useCallback(
    (index: number) => setCurrent((index + total) % total),
    [total],
  );

  const visibleIndices = useVisibleIndices(current, total);

  return (
    <PanelWrapper backLabel={backLabel} onBack={onBack} isRtl={isRtl}>
      <div dir={isRtl ? "rtl" : "ltr"} className="space-y-12">
        <h2 className="text-center text-2xl font-bold text-secondary sm:text-3xl lg:text-4xl">
          {content.title}
        </h2>

        <div className="relative px-2 sm:px-8">
          <button
            type="button"
            onClick={() => goTo(current - 1)}
            className="absolute top-1/2 z-10 flex size-12 -translate-y-1/2 items-center justify-center rounded-full bg-secondary text-white shadow-lg transition-opacity hover:opacity-90 sm:size-14 ltr:-left-2 sm:ltr:-left-4 rtl:-right-2 sm:rtl:-right-4"
            aria-label="Previous"
          >
            <ChevronLeft className="size-6 rtl:rotate-180" />
          </button>

          <div className="flex items-stretch justify-center gap-4 overflow-hidden sm:gap-6">
            {visibleIndices.map((index) => {
              const expert = content.experts[index];
              const isCenter = index === current;

              return (
                <article
                  key={expert.name}
                  className={cn(
                    "w-[260px] shrink-0 overflow-hidden rounded-[24px] bg-white shadow-[1px_1px_18px_0px_#111F4214] transition-all duration-300 sm:w-[300px] lg:w-[340px]",
                    !isCenter && "hidden sm:block sm:opacity-70",
                  )}
                >
                  <div className="relative aspect-[340/380] bg-[#eef4f8]">
                    <span
                      className="absolute bottom-0 end-0 h-[70%] w-[55%] rounded-tl-[80px] bg-primary/20"
                      aria-hidden
                    />
                    <Image
                      src={expert.image}
                      alt={expert.name}
                      fill
                      className="object-cover object-top"
                      sizes="340px"
                    />
                  </div>
                  <div className="space-y-1 p-5 text-center">
                    <p className="text-sm text-muted-foreground">{expert.specialty}</p>
                    <h3 className="text-base font-bold text-primary sm:text-lg">
                      {expert.name}
                    </h3>
                  </div>
                </article>
              );
            })}
          </div>

          <button
            type="button"
            onClick={() => goTo(current + 1)}
            className="absolute top-1/2 z-10 flex size-12 -translate-y-1/2 items-center justify-center rounded-full bg-secondary text-white shadow-lg transition-opacity hover:opacity-90 sm:size-14 ltr:-right-2 sm:ltr:-right-4 rtl:-left-2 sm:rtl:-left-4"
            aria-label="Next"
          >
            <ChevronRight className="size-6 rtl:rotate-180" />
          </button>
        </div>
      </div>
    </PanelWrapper>
  );
}
