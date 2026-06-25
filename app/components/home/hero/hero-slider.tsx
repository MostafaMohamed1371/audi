"use client";

import Image from "next/image";
import { useCallback, useState } from "react";
import { ChevronDown, ChevronUp, Cpu, Globe2, Layers, Sparkles } from "lucide-react";
import { cn } from "@/lib/utils";

type Slide = {
  image: string;
  title: string;
};

type HeroSliderProps = {
  slides: Slide[];
};

const slideIcons = [Globe2, Cpu, Layers, Sparkles];

export function HeroSlider({ slides }: HeroSliderProps) {
  const [current, setCurrent] = useState(0);
  const total = slides.length;
  const canNavigate = total > 1;

  const goTo = useCallback(
    (index: number) => {
      if (!canNavigate) return;
      setCurrent((index + total) % total);
    },
    [canNavigate, total]
  );

  const goNext = useCallback(() => goTo(current + 1), [current, goTo]);
  const goPrev = useCallback(() => goTo(current - 1), [current, goTo]);

  if (total === 0) {
    return null;
  }

  return (
    <section className="relative h-[min(calc(100svh-80px),640px)] min-h-[400px] w-full overflow-hidden bg-secondary sm:min-h-[480px] lg:min-h-[520px] lg:h-[calc(100vh-80px)]">
      <div
        className="h-full transition-transform duration-700 ease-in-out"
        style={{ transform: `translateY(-${current * 100}%)` }}
      >
        {slides.map((slide) => (
          <div
            key={`${slide.image}-${slide.title}`}
            className="relative h-[min(calc(100svh-80px),640px)] min-h-[400px] w-full sm:min-h-[480px] lg:min-h-[520px] lg:h-[calc(100vh-80px)]"
          >
            <Image
              src={slide.image}
              alt={slide.title}
              fill
              priority
              className="object-cover"
              sizes="100vw"
            />
            <div className="absolute inset-0 bg-linear-to-t from-black/50 via-black/10 to-transparent" />
          </div>
        ))}
      </div>

      <div className="absolute top-1/2 start-3 z-20 flex -translate-y-1/2 flex-col items-center gap-2 sm:start-4 sm:gap-3">
        <button
          type="button"
          onClick={goPrev}
          disabled={!canNavigate}
          aria-label="Previous slide"
          className="flex size-8 items-center justify-center rounded-full bg-primary text-primary-foreground shadow-md transition-transform hover:scale-105 disabled:cursor-not-allowed disabled:opacity-40 cursor-pointer sm:size-9"
        >
          <ChevronUp className="size-3.5 sm:size-4" />
        </button>

        <div className="flex flex-col gap-1.5 sm:gap-2">
          {slides.map((slide, index) => {
            const Icon = slideIcons[index % slideIcons.length];
            const isActive = index === current;

            return (
              <button
                key={`${slide.image}-${slide.title}-nav`}
                type="button"
                onClick={() => goTo(index)}
                disabled={!canNavigate && index !== 0}
                aria-label={slide.title}
                aria-current={isActive ? "true" : undefined}
                className={cn(
                  "flex size-8 items-center justify-center rounded-md border transition-all cursor-pointer sm:size-9",
                  isActive
                    ? "border-primary bg-primary text-primary-foreground"
                    : "border-white/20 bg-black/30 text-white/70 hover:bg-black/50 hover:text-white",
                  !canNavigate && !isActive && "opacity-40"
                )}
              >
                <Icon className="size-3.5 sm:size-4" />
              </button>
            );
          })}
        </div>

        <button
          type="button"
          onClick={goNext}
          disabled={!canNavigate}
          aria-label="Next slide"
          className="flex size-8 items-center justify-center rounded-full bg-primary text-primary-foreground shadow-md transition-transform hover:scale-105 disabled:cursor-not-allowed disabled:opacity-40 cursor-pointer sm:size-9"
        >
          <ChevronDown className="size-3.5 sm:size-4" />
        </button>
      </div>

      <div className="pointer-events-none absolute inset-x-0 bottom-0 z-20 overflow-hidden sm:inset-x-auto sm:start-auto sm:end-0">
        <div className="relative max-w-full sm:max-w-none">
          <div className="absolute -end-10 -bottom-10 size-48 rounded-full bg-primary/90 transition-opacity duration-500 sm:-end-16 sm:-bottom-16 sm:size-72" />
          <h2
            key={slides[current].title}
            className="relative max-w-full px-14 py-8 text-2xl font-bold leading-tight text-white animate-in fade-in slide-in-from-bottom-4 duration-500 sm:px-14 sm:py-16 sm:text-4xl lg:text-5xl"
          >
            {slides[current].title}
          </h2>
        </div>
      </div>
    </section>
  );
}
