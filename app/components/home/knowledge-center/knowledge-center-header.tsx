"use client";

import Image from "next/image";
import { useEffect, useState } from "react";
import { cn } from "@/lib/utils";

type HeaderSlide = {
  title: string;
  description: string;
  icon: string;
};

type Props = {
  slides: HeaderSlide[];
  isRtl: boolean;
};

const INTERVAL_MS = 2000;

export function KnowledgeCenterHeader({ slides, isRtl }: Props) {
  const [current, setCurrent] = useState(0);
  const total = slides.length;

  useEffect(() => {
    if (total <= 1) return;

    const id = setInterval(() => {
      setCurrent((prev) => (prev + 1) % total);
    }, INTERVAL_MS);

    return () => clearInterval(id);
  }, [total]);

  if (total === 0) return null;

  return (
    <div
      className={cn(
        "flex flex-col items-center gap-6 text-center sm:gap-8 lg:flex-row lg:items-center lg:gap-16 lg:text-start",
        isRtl ? "lg:flex-row lg:text-right" : "lg:flex-row-reverse lg:text-left",
      )}
    >
      <div className="relative h-24 w-40 shrink-0 sm:h-32 sm:w-56">
        {slides.map((item, index) => (
          <Image
            key={item.icon}
            src={`/knowledgeCenter/${item.icon}`}
            alt=""
            fill
            className={cn(
              "object-contain object-center transition-opacity duration-500",
              index === current ? "opacity-100" : "pointer-events-none opacity-0",
            )}
            sizes="(max-width: 1024px) 200px, 224px"
            priority={index === 0}
          />
        ))}
      </div>

      <div
        className={cn(
          "relative min-h-[100px] flex-1 sm:min-h-[140px]",
          isRtl ? "lg:text-right" : "lg:text-left",
        )}
      >
        {slides.map((item, index) => (
          <div
            key={item.title}
            className={cn(
              "transition-opacity duration-500",
              index === current
                ? "relative opacity-100"
                : "pointer-events-none absolute inset-0 opacity-0",
            )}
            aria-hidden={index !== current}
          >
            <h2 className="mb-3 text-xl font-bold text-secondary sm:mb-4 sm:text-3xl lg:text-4xl">
              {item.title}
            </h2>
            <p className="text-sm leading-7 text-[#4d5a6f] sm:text-base sm:leading-8">
              {item.description}
            </p>
          </div>
        ))}
      </div>
    </div>
  );
}
