"use client";

import Image from "next/image";
import { ChevronLeft, ChevronRight } from "lucide-react";
import { useCallback, useEffect, useRef, useState } from "react";
import { cn } from "@/lib/utils";

type Props = {
  images: string[];
  title: string;
  isRtl: boolean;
};

type CarouselMetrics = {
  containerWidth: number;
  cardWidth: number;
  gap: number;
  step: number;
};

const GAP = 24;
const DESKTOP_VISIBLE_CARDS = 2.5;
const MOBILE_VISIBLE_CARDS = 1.5;

function getCardWidth(containerWidth: number) {
  if (containerWidth < 640) {
    return (containerWidth - GAP) / MOBILE_VISIBLE_CARDS;
  }
  return (containerWidth - GAP * 2) / DESKTOP_VISIBLE_CARDS;
}

function buildLoopSlides(images: string[]) {
  if (images.length <= 1) return images;
  return [...images, ...images, ...images];
}

export function InstituteHeadquartersCarousel({
  images,
  title,
  isRtl,
}: Props) {
  const total = images.length;
  const canNavigate = total > 1;
  const loopSlides = buildLoopSlides(images);

  const [index, setIndex] = useState(total);
  const [animate, setAnimate] = useState(true);
  const [metrics, setMetrics] = useState<CarouselMetrics>({
    containerWidth: 0,
    cardWidth: 0,
    gap: GAP,
    step: 0,
  });
  const containerRef = useRef<HTMLDivElement>(null);

  const resetWithoutAnimation = useCallback((nextIndex: number) => {
    setAnimate(false);
    setIndex(nextIndex);
    requestAnimationFrame(() => {
      requestAnimationFrame(() => setAnimate(true));
    });
  }, []);

  const goNext = useCallback(() => {
    if (!canNavigate) return;
    setAnimate(true);
    setIndex((prev) => prev + 1);
  }, [canNavigate]);

  const goPrev = useCallback(() => {
    if (!canNavigate) return;
    setAnimate(true);
    setIndex((prev) => prev - 1);
  }, [canNavigate]);

  const handleTransitionEnd = useCallback(
    (event: React.TransitionEvent<HTMLDivElement>) => {
      if (event.propertyName !== "transform" || !canNavigate) return;

      if (index >= total * 2) {
        resetWithoutAnimation(((index - total) % total) + total);
      } else if (index < total) {
        resetWithoutAnimation((index % total) + total);
      }
    },
    [canNavigate, index, resetWithoutAnimation, total],
  );

  useEffect(() => {
    const container = containerRef.current;
    if (!container) return;

    const measure = () => {
      const width = container.offsetWidth;
      const cardWidth = getCardWidth(width);
      setMetrics({
        containerWidth: width,
        cardWidth,
        gap: GAP,
        step: cardWidth + GAP,
      });
    };

    measure();
    const observer = new ResizeObserver(measure);
    observer.observe(container);

    return () => observer.disconnect();
  }, []);

  if (total === 0) return null;

  const halfCard = metrics.cardWidth / 2;
  const baseOffset =
    metrics.containerWidth >= 640 ? halfCard : 0;
  const offset = baseOffset + metrics.step * index;

  return (
    <section className="bg-background py-16 sm:py-20 lg:py-24">
      <div className="mx-auto max-w-7xl px-4 sm:px-6">
        <div
          dir={isRtl ? "rtl" : "ltr"}
          className="mb-8 flex items-center justify-between gap-4 sm:mb-10"
        >
          <h2 className="relative inline-block pb-4 text-2xl font-bold text-secondary sm:text-3xl lg:text-4xl">
            {title}
            <span
              className="absolute start-0 bottom-0 h-1.5 w-[calc(100%+0.75rem)] bg-primary"
              aria-hidden
            />
          </h2>

          {canNavigate ? (
            <div dir="ltr" className="flex shrink-0 gap-3">
              <button
                type="button"
                onClick={goPrev}
                aria-label={isRtl ? "السابق" : "Previous"}
                className="flex size-11 items-center justify-center rounded-full bg-primary text-white transition-colors hover:bg-primary/90 cursor-pointer"
              >
                <ChevronLeft className="size-5" />
              </button>
              <button
                type="button"
                onClick={goNext}
                aria-label={isRtl ? "التالي" : "Next"}
                className="flex size-11 items-center justify-center rounded-full bg-primary text-white transition-colors hover:bg-primary/90 cursor-pointer"
              >
                <ChevronRight className="size-5" />
              </button>
            </div>
          ) : null}
        </div>

        <div ref={containerRef} dir="ltr" className="overflow-hidden">
          <div
            className={cn(
              "flex",
              animate && "transition-transform duration-500 ease-out",
            )}
            style={{
              gap: metrics.gap,
              transform: `translateX(-${offset}px)`,
            }}
            onTransitionEnd={handleTransitionEnd}
          >
            {loopSlides.map((image, slideIndex) => (
              <div
                key={`${image}-${slideIndex}`}
                style={
                  metrics.cardWidth > 0
                    ? { width: metrics.cardWidth }
                    : undefined
                }
                className="relative aspect-659/499 w-[calc((100%-1.5rem)/1.5)] shrink-0 overflow-hidden rounded-3xl sm:w-[calc((100%-3rem)/2.5)]"
              >
                <Image
                  src={image}
                  alt=""
                  fill
                  className="object-cover"
                  sizes="(max-width: 640px) 66vw, 40vw"
                  priority={slideIndex >= total && slideIndex < total * 2}
                />
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}
