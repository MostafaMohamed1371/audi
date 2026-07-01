"use client";

import Image from "next/image";
import { ChevronLeft, ChevronRight } from "lucide-react";
import { useCallback, useState } from "react";
import { cn } from "@/lib/utils";
import { FormatBox } from "@/app/components/programs/training/shared/format-box";
import { PanelWrapper } from "@/app/components/programs/training/shared/panel-wrapper";
import type {
  ExecutiveContent,
  TrainingPanelProps,
} from "@/app/components/programs/training/shared/types";

type Props = TrainingPanelProps & {
  content: ExecutiveContent;
};

export function ExecutivePanel({
  content,
  isRtl,
  backLabel,
  onBack,
}: Props) {
  const [current, setCurrent] = useState(1);
  const total = content.topics.length;
  const heroVideo = content.heroVideo ?? "/icons/program/executive.mp4";

  const goTo = useCallback(
    (index: number) => setCurrent((index + total) % total),
    [total],
  );

  return (
    <div className="space-y-0">
      <PanelWrapper backLabel={backLabel} onBack={onBack} isRtl={isRtl}>
        <div
          dir={isRtl ? "rtl" : "ltr"}
          className="grid items-center gap-10 lg:grid-cols-12 lg:gap-12 xl:gap-16"
        >
          <div className="mx-auto w-full max-w-[614px] lg:col-span-5 lg:mx-0 lg:justify-self-end">
            <div className="aspect-[614/538] overflow-hidden rounded-2xl border border-primary/30 bg-white">
              <video
                autoPlay
                loop
                muted
                playsInline
                className="size-full object-cover"
                aria-hidden
              >
                <source src={heroVideo} type="video/mp4" />
              </video>
            </div>
          </div>

          <div
            dir={isRtl ? "rtl" : "ltr"}
            className="space-y-6 text-start lg:col-span-7"
          >
            <h2 className="text-2xl font-bold text-secondary sm:text-3xl lg:text-4xl">
              {content.title}
            </h2>
            <p className="text-base leading-8 text-muted-foreground sm:text-lg sm:leading-9">
              {content.intro}
            </p>
            <h3 className="text-xl font-bold text-secondary sm:text-2xl">
              {content.offersTitle}
            </h3>
            <div className="space-y-3">
              {content.programs.map((program) => (
                <FormatBox key={program} text={program} isRtl={isRtl} />
              ))}
            </div>
          </div>
        </div>
      </PanelWrapper>

      <section className="mt-14 bg-secondary px-4 py-14 sm:mt-16 sm:px-6 sm:py-16 lg:py-20">
        <div dir={isRtl ? "rtl" : "ltr"} className="mx-auto max-w-7xl space-y-10">
          <h3 className="text-center text-2xl font-bold text-white sm:text-3xl">
            {content.topicsTitle}
          </h3>

          <div>
            <div className="relative mx-auto h-[340px] w-full max-w-5xl sm:h-[400px] lg:h-[440px]">
              {content.topics.map((topic, index) => {
                let offset = index - current;
                if (offset > total / 2) offset -= total;
                if (offset < -total / 2) offset += total;

                const isCenter = offset === 0;
                const isAdjacent = Math.abs(offset) === 1;

                if (!isCenter && !isAdjacent) return null;

                const slideX = isCenter
                  ? 0
                  : (isRtl ? -offset : offset) * 108;

                return (
                  <article
                    key={topic.title}
                    className="absolute left-1/2 top-0 w-[min(88vw,300px)] transition-[transform,opacity] duration-500 ease-[cubic-bezier(0.4,0,0.2,1)] will-change-[transform,opacity] sm:w-[340px] lg:w-[380px]"
                    style={{
                      transform: `translateX(calc(-50% + ${slideX}%)) scale(${isCenter ? 1 : 0.74})`,
                      opacity: isCenter ? 1 : 0.42,
                      zIndex: isCenter ? 20 : 10,
                    }}
                  >
                    <div className="overflow-hidden rounded-[20px]">
                      <div className="relative aspect-[380/300] overflow-hidden">
                        <Image
                          src={`/projects/${topic.image}`}
                          alt={topic.title}
                          width={380}
                          height={300}
                          className="h-full w-full object-cover transition-transform duration-500"
                          sizes="380px"
                        />
                        {!isCenter ? (
                          <div
                            className="absolute inset-0 bg-secondary/55 transition-opacity duration-500"
                            aria-hidden
                          />
                        ) : null}
                      </div>
                      <p
                        className={cn(
                          "bg-secondary p-4 text-center text-base font-bold text-white transition-all duration-500 sm:text-lg",
                          isCenter
                            ? "max-h-24 opacity-100"
                            : "max-h-0 overflow-hidden p-0 opacity-0",
                        )}
                      >
                        {topic.title}
                      </p>
                    </div>
                  </article>
                );
              })}
            </div>

            <div className="mt-8 flex items-center justify-center gap-4">
              <button
                type="button"
                onClick={() => goTo(current - 1)}
                className="flex size-12 items-center justify-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20"
                aria-label="Previous"
              >
                <ChevronLeft className="size-5 rtl:rotate-180" />
              </button>
              <button
                type="button"
                onClick={() => goTo(current + 1)}
                className="flex size-12 items-center justify-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20"
                aria-label="Next"
              >
                <ChevronRight className="size-5 rtl:rotate-180" />
              </button>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
