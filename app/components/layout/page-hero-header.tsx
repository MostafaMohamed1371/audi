import Image from "next/image";
import { cn } from "@/lib/utils";

export const DEFAULT_PAGE_HERO_IMAGE = "/header/about.png";
export const DEFAULT_PAGE_HERO_OVERLAY = "#111F42B8";

type PageHeroHeaderProps = {
  title: string;
  image?: string;
  imageAlt?: string;
  className?: string;
  contentClassName?: string;
  titleClassName?: string;
  backgroundColor?: string;
};

export function PageHeroHeader({
  title,
  image = DEFAULT_PAGE_HERO_IMAGE,
  imageAlt = "",
  className,
  contentClassName,
  titleClassName,
  backgroundColor = DEFAULT_PAGE_HERO_OVERLAY,
}: PageHeroHeaderProps) {
  return (
    <section
      className={cn(
        "relative flex min-h-[280px] items-end overflow-hidden sm:min-h-[340px] lg:min-h-[450px]",
        className
      )}
    >
      <Image
        src={image}
        alt={imageAlt}
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
      <div
        className={cn(
          "relative z-10 mx-auto w-full max-w-7xl px-4 pb-10 pt-24 sm:px-6 sm:pb-12 sm:pt-28",
          contentClassName,
        )}
      >
        <h1
          className={cn(
            "text-start text-3xl font-bold text-white sm:text-4xl lg:text-5xl",
            titleClassName,
          )}
        >
          {title}
        </h1>
      </div>
    </section>
  );
}
