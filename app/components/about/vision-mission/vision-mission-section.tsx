"use client";

import { cn } from "@/lib/utils";
import { ChevronLeft } from "lucide-react";
import Image from "next/image";
import { useEffect, useRef, useState } from "react";

const TRANSITION_MS = 500;

type CardData = {
  title: string;
  text: string;
  readMore: string;
  image: string;
};

type Props = {
  isRtl: boolean;
  vision: CardData;
  mission: CardData;
};

export function VisionMissionSection({ isRtl, vision, mission }: Props) {
  return (
    <section
      dir={isRtl ? "rtl" : "ltr"}
      className="relative bg-[#f4f7f9] py-16 sm:py-20 lg:py-24"
    >
      <div className="pointer-events-none absolute inset-0 overflow-hidden">
        <div className="absolute -inset-s-32 top-1/4 size-[420px] rounded-full bg-primary/10 blur-3xl" />
        <div className="absolute -inset-e-32 bottom-1/4 size-[420px] rounded-full bg-primary/10 blur-3xl" />
      </div>

      <div className="relative mx-auto max-w-7xl px-4 sm:px-6">
        <div className="grid gap-16 lg:grid-cols-2 lg:gap-40 xl:gap-52 2xl:gap-64">
          <VisionMissionCard data={vision} variant="vision" isRtl={isRtl} />
          <VisionMissionCard
            data={mission}
            variant="mission"
            isRtl={isRtl}
            className="lg:mt-24"
          />
        </div>
      </div>
    </section>
  );
}

type CardProps = {
  data: CardData;
  variant: "vision" | "mission";
  isRtl: boolean;
  className?: string;
};

function VisionMissionCard({ data, variant, isRtl, className }: CardProps) {
  const [isMounted, setIsMounted] = useState(false);
  const [isActive, setIsActive] = useState(false);
  const imageRef = useRef<HTMLDivElement>(null);
  const closeTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null);
  const unmountTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null);
  const isVision = variant === "vision";

  const clearTimers = () => {
    if (closeTimerRef.current) {
      clearTimeout(closeTimerRef.current);
      closeTimerRef.current = null;
    }
    if (unmountTimerRef.current) {
      clearTimeout(unmountTimerRef.current);
      unmountTimerRef.current = null;
    }
  };

  useEffect(() => clearTimers, []);

  const openPanel = () => {
    clearTimers();
    setIsMounted(true);
    requestAnimationFrame(() => {
      requestAnimationFrame(() => setIsActive(true));
    });
  };

  const closePanel = () => {
    clearTimers();
    setIsActive(false);
    unmountTimerRef.current = setTimeout(() => {
      setIsMounted(false);
    }, TRANSITION_MS);
  };

  const scheduleClose = () => {
    clearTimers();
    closeTimerRef.current = setTimeout(closePanel, 120);
  };

  const handleCardLeave = (event: React.MouseEvent<HTMLElement>) => {
    const related = event.relatedTarget as Node | null;
    if (related && imageRef.current?.contains(related)) return;
    scheduleClose();
  };

  const handleImageEnter = () => {
    clearTimers();
    if (!isMounted) {
      openPanel();
      return;
    }
    setIsActive(true);
  };

  const handleImageLeave = (event: React.MouseEvent<HTMLElement>) => {
    const related = event.relatedTarget as Node | null;
    if (related && event.currentTarget.contains(related)) return;
    closePanel();
  };

  return (
    <div className={cn("relative", className)}>
      <div
        className={cn(
          "transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]",
          isActive && "pointer-events-none opacity-0"
        )}
      >
        <h2 className="mb-6 text-2xl font-bold text-secondary sm:text-3xl lg:text-4xl">
          {data.title}
        </h2>

        <article
          onMouseEnter={openPanel}
          onMouseLeave={handleCardLeave}
          className="flex min-h-[300px] cursor-pointer flex-col justify-between rounded-2xl bg-primary p-8 transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] hover:scale-[1.01] sm:min-h-[340px] sm:p-10"
        >
          <p className="text-start text-base leading-9 text-white sm:text-lg sm:leading-10">
            {data.text}
          </p>
          <span className="mt-8 inline-flex items-center gap-1 self-start text-sm font-medium text-white/90">
            {data.readMore}
            <ChevronLeft className="size-4 rtl:rotate-0 ltr:rotate-180" />
          </span>
        </article>
      </div>

      {isMounted && (
        <div
          className={cn(
            "pointer-events-none fixed inset-0 z-[100] flex flex-col bg-primary transition-opacity duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]",
            isActive ? "opacity-100" : "opacity-0"
          )}
        >
          <div className="mx-auto flex h-full w-full max-w-7xl flex-col px-4 py-10 sm:px-6 sm:py-14 lg:py-16">
            <h2
              className={cn(
                "pointer-events-none mb-8 text-3xl font-bold text-white transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] sm:mb-10 sm:text-4xl lg:text-5xl",
                isVision
                  ? isRtl
                    ? "self-start"
                    : "self-end"
                  : isRtl
                    ? "self-end"
                    : "self-start",
                isActive ? "translate-y-0 opacity-100" : "-translate-y-4 opacity-0"
              )}
            >
              {data.title}
            </h2>

            <div className="flex flex-1 items-center justify-center">
              <div
                className={cn(
                  "pointer-events-none flex w-full max-w-5xl items-center",
                  isVision
                    ? isRtl
                      ? "flex-row-reverse"
                      : "flex-row"
                    : isRtl
                      ? "flex-row"
                      : "flex-row-reverse"
                )}
              >
                <div
                  className={cn(
                    "relative z-10 flex min-h-[235px] w-full max-w-xl shrink-0 items-center justify-center rounded-lg bg-[#00273729] px-10 py-12 transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] sm:min-h-[270px] sm:px-12 sm:py-14 lg:max-w-2xl",
                    isActive
                      ? "translate-x-0 opacity-100"
                      : isVision
                        ? isRtl
                          ? "translate-x-8 opacity-0"
                          : "-translate-x-8 opacity-0"
                        : isRtl
                          ? "-translate-x-8 opacity-0"
                          : "translate-x-8 opacity-0"
                  )}
                >
                  <p className="mx-auto max-w-md text-balance text-center text-lg leading-[2.2] text-white sm:max-w-lg sm:text-xl sm:leading-[2.3]">
                    {data.text}
                  </p>
                </div>

                <div
                  ref={imageRef}
                  onMouseEnter={handleImageEnter}
                  onMouseLeave={handleImageLeave}
                  className={cn(
                    "pointer-events-auto relative z-0 w-full max-w-[313px] shrink-0 cursor-pointer transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] sm:max-w-[345px] lg:max-w-[409px]",
                    isActive ? "scale-100 opacity-100" : "scale-95 opacity-0",
                    isVision
                      ? "-ms-8 sm:-ms-14 lg:-ms-20"
                      : "-me-8 sm:-me-14 lg:-me-20"
                  )}
                >
                  <div className="relative aspect-3/4 w-full overflow-hidden rounded-3xl shadow-[0_24px_64px_rgba(0,0,0,0.25)] ">
                    <Image
                      src={data.image}
                      alt={data.title}
                      fill
                      className="object-cover"
                      sizes="(max-width: 768px) 313px, 409px"
                      priority
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
