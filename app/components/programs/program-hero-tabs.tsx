"use client";

import Image from "next/image";
import { cn } from "@/lib/utils";
import { useCallback, useLayoutEffect, useRef, useState } from "react";

const TAB_CARD_GRADIENT =
  "linear-gradient(180deg, rgba(94, 94, 94, 0) 0%, rgba(0, 112, 158, 0.58) 100%)";

export type ProgramHeroTab<T extends string> = {
  id: T;
  label: string;
  video?: string;
  previewImage?: string;
};

type Props<T extends string> = {
  tabs: ProgramHeroTab<T>[];
  activeTab: T;
  onTabChange: (tab: T) => void;
  ariaLabel?: string;
};

type IndicatorStyle = {
  left: number;
  width: number;
};

function TabPreview({
  video,
  previewImage,
}: {
  video?: string;
  previewImage?: string;
}) {
  if (video) {
    return (
      <video
        autoPlay
        loop
        muted
        playsInline
        className="absolute inset-0 size-full object-cover"
      >
        <source src={video} type="video/mp4" />
      </video>
    );
  }

  if (previewImage) {
    if (previewImage.endsWith(".gif")) {
      return (
        // eslint-disable-next-line @next/next/no-img-element
        <img
          src={previewImage}
          alt=""
          className="absolute inset-0 size-full object-cover"
          aria-hidden
        />
      );
    }

    return (
      <Image
        src={previewImage}
        alt=""
        fill
        className="object-cover"
        sizes="280px"
        aria-hidden
      />
    );
  }

  return null;
}

export function ProgramHeroTabs<T extends string>({
  tabs,
  activeTab,
  onTabChange,
  ariaLabel = "Program tabs",
}: Props<T>) {
  const containerRef = useRef<HTMLDivElement>(null);
  const tabRefs = useRef<(HTMLDivElement | null)[]>([]);
  const mobileScrollRef = useRef<HTMLDivElement>(null);
  const activeMobileTabRef = useRef<HTMLButtonElement>(null);
  const [hoveredTab, setHoveredTab] = useState<T | null>(null);
  const [indicator, setIndicator] = useState<IndicatorStyle>({ left: 0, width: 0 });

  const displayTab = hoveredTab ?? activeTab;

  const updateIndicator = useCallback(() => {
    const index = tabs.findIndex((tab) => tab.id === displayTab);
    const tabEl = tabRefs.current[index];
    const containerEl = containerRef.current;
    if (!tabEl || !containerEl) return;

    const containerRect = containerEl.getBoundingClientRect();
    const tabRect = tabEl.getBoundingClientRect();

    setIndicator({
      left: tabRect.left - containerRect.left,
      width: tabRect.width,
    });
  }, [displayTab, tabs]);

  useLayoutEffect(() => {
    updateIndicator();
  }, [updateIndicator]);

  useLayoutEffect(() => {
    const containerEl = containerRef.current;
    if (!containerEl) return;

    const observer = new ResizeObserver(updateIndicator);
    observer.observe(containerEl);
    window.addEventListener("resize", updateIndicator);

    return () => {
      observer.disconnect();
      window.removeEventListener("resize", updateIndicator);
    };
  }, [updateIndicator]);

  useLayoutEffect(() => {
    activeMobileTabRef.current?.scrollIntoView({
      inline: "center",
      block: "nearest",
      behavior: "smooth",
    });
  }, [activeTab]);

  const displayVideo =
    tabs.find((tab) => tab.id === displayTab)?.video ??
    tabs.find((tab) => tab.video)?.video;

  const displayImage =
    tabs.find((tab) => tab.id === displayTab)?.previewImage ??
    tabs.find((tab) => tab.previewImage)?.previewImage;

  return (
    <nav aria-label={ariaLabel} className="w-full">
      <div className="lg:hidden">
        <div
          ref={mobileScrollRef}
          className="-mx-4 flex gap-3 overflow-x-auto px-4 pb-1 snap-x snap-mandatory [-ms-overflow-style:none] scrollbar-none [&::-webkit-scrollbar]:hidden"
        >
          {tabs.map((tab) => {
            const isActive = activeTab === tab.id;

            return (
              <button
                key={tab.id}
                ref={isActive ? activeMobileTabRef : undefined}
                type="button"
                onClick={() => onTabChange(tab.id)}
                aria-current={isActive ? "page" : undefined}
                className={cn(
                  "w-[min(78vw,260px)] shrink-0 snap-center overflow-hidden rounded-2xl border-2 text-start transition-all duration-300",
                  isActive
                    ? "border-white shadow-[0_0_24px_rgba(0,112,158,0.45)]"
                    : "border-white/25 opacity-85 active:opacity-100",
                )}
              >
                <div className="relative h-[88px] w-full overflow-hidden sm:h-[96px]">
                  <TabPreview
                    video={tab.video}
                    previewImage={tab.previewImage}
                  />
                  <div
                    className="absolute inset-0"
                    style={{ background: TAB_CARD_GRADIENT }}
                    aria-hidden
                  />
                </div>
                <span
                  className={cn(
                    "block px-3 py-3 text-sm leading-snug text-white",
                    isActive ? "font-bold" : "font-normal",
                  )}
                >
                  {tab.label}
                </span>
              </button>
            );
          })}
        </div>
      </div>

      <div ref={containerRef} className="relative hidden lg:block">
        <div
          className="pointer-events-none absolute top-0 z-10 h-[144px] overflow-hidden rounded-2xl border-2 border-white/25 bg-[#00709e]/10 shadow-[0_8px_32px_rgba(0,112,158,0.28)] transition-[left,width] duration-500 ease-out"
          style={{
            left: indicator.width ? indicator.left : 0,
            width: indicator.width || undefined,
            opacity: indicator.width ? 1 : 0,
          }}
          aria-hidden
        >
          <div className="relative size-full">
            {displayVideo ? (
              <video
                key={displayVideo}
                autoPlay
                loop
                muted
                playsInline
                className="absolute inset-0 size-full object-cover"
              >
                <source src={displayVideo} type="video/mp4" />
              </video>
            ) : displayImage ? (
              displayImage.endsWith(".gif") ? (
                // eslint-disable-next-line @next/next/no-img-element
                <img
                  key={displayImage}
                  src={displayImage}
                  alt=""
                  className="absolute inset-0 size-full object-cover"
                  aria-hidden
                />
              ) : (
                <Image
                  key={displayImage}
                  src={displayImage}
                  alt=""
                  fill
                  className="object-cover"
                  sizes="400px"
                  aria-hidden
                />
              )
            ) : null}
            <div
              className="absolute inset-0"
              style={{ background: TAB_CARD_GRADIENT }}
            />
          </div>
        </div>

        <div className="flex items-end pt-[152px]">
          {tabs.map((tab, index) => {
            const isActive = displayTab === tab.id;

            return (
              <div key={tab.id} className="flex min-w-0 flex-1 items-stretch">
                {index > 0 ? (
                  <span
                    className="mb-0 mt-4 h-7 w-px shrink-0 self-stretch bg-white/40"
                    aria-hidden
                  />
                ) : null}

                <div
                  ref={(node) => {
                    tabRefs.current[index] = node;
                  }}
                  className="min-w-0 flex-1"
                >
                  <button
                    type="button"
                    onClick={() => onTabChange(tab.id)}
                    onMouseEnter={() => setHoveredTab(tab.id)}
                    onMouseLeave={() => setHoveredTab(null)}
                    onFocus={() => setHoveredTab(tab.id)}
                    onBlur={() => setHoveredTab(null)}
                    aria-current={activeTab === tab.id ? "page" : undefined}
                    className={cn(
                      "flex w-full cursor-pointer items-center justify-center px-4 py-5 text-[15px] leading-snug text-white transition-[font-weight,opacity] duration-300 hover:opacity-100",
                      isActive ? "font-bold opacity-100" : "font-normal opacity-75",
                    )}
                  >
                    <span className="text-center">{tab.label}</span>
                  </button>
                </div>
              </div>
            );
          })}
        </div>
      </div>

      <div className="mt-4 h-px w-full bg-white/50 lg:mt-0" aria-hidden />
    </nav>
  );
}
