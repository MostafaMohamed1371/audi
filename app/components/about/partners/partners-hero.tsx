import {
  DEFAULT_PAGE_HERO_IMAGE,
  DEFAULT_PAGE_HERO_OVERLAY,
} from "@/app/components/layout/page-hero-header";
import { cn } from "@/lib/utils";
import Image from "next/image";

type Props = {
  title: string;
  description: string;
  isRtl: boolean;
  image?: string;
  backgroundColor?: string;
  minHeightClassName?: string;
};

const DEFAULT_MIN_HEIGHT =
  "min-h-[320px] sm:min-h-[380px] lg:min-h-[420px]";

export function PartnersHero({
  title,
  description,
  isRtl,
  image = DEFAULT_PAGE_HERO_IMAGE,
  backgroundColor = DEFAULT_PAGE_HERO_OVERLAY,
  minHeightClassName = DEFAULT_MIN_HEIGHT,
}: Props) {
  return (
    <section className="relative overflow-hidden">
      <div className={cn("relative", minHeightClassName)}>
        <Image
          src={image}
          alt=""
          fill
          priority
          className="object-cover"
          sizes="100vw"
        />
        <div
          className="absolute inset-0"
          style={{ backgroundColor }}
          aria-hidden
        />

        <div className="relative z-10 mx-auto max-w-7xl px-4 pb-24 pt-32 sm:px-6 sm:pb-28 sm:pt-36 lg:pb-32 lg:pt-40">
          <div
            dir={isRtl ? "rtl" : "ltr"}
            className={cn(
              "grid w-full gap-8 lg:grid-cols-2 lg:items-center lg:gap-16 xl:gap-24",
            )}
          >
            <h1 className="text-start text-3xl font-bold text-white sm:text-4xl lg:text-5xl">
              {title}
            </h1>
            <p className="text-start text-base leading-8 text-white/90 sm:text-lg sm:leading-9">
              {description}
            </p>
          </div>
        </div>
      </div>
    </section>
  );
}
