import { ButtonLink } from "@/app/components/ui/button";
import {
  DEFAULT_PAGE_HERO_IMAGE,
  DEFAULT_PAGE_HERO_OVERLAY,
} from "@/app/components/layout/page-hero-header";
import { BookOpen } from "lucide-react";
import Image from "next/image";

type Props = {
  title: string;
  bookletLabel: string;
  bookletHref: string;
  isRtl: boolean;
  image?: string;
  backgroundColor?: string;
};

export function Strategy2025Hero({
  title,
  bookletLabel,
  bookletHref,
  isRtl,
  image = DEFAULT_PAGE_HERO_IMAGE,
  backgroundColor = DEFAULT_PAGE_HERO_OVERLAY,
}: Props) {
  return (
    <section className="relative overflow-hidden">
      <div className="relative min-h-[320px] sm:min-h-[380px] lg:min-h-[450px]">
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

        <div className="relative z-10 mx-auto max-w-7xl px-4 pb-20 pt-32 sm:px-6 sm:pb-24 sm:pt-36 lg:pb-28 lg:pt-40">
          <div
            dir={isRtl ? "rtl" : "ltr"}
            className="flex w-full flex-col items-start gap-6"
          >
            <h1 className="text-start text-3xl font-bold text-white sm:text-4xl lg:text-5xl">
              {title}
            </h1>

            <ButtonLink
              size="lg"
              className="rounded-full bg-primary px-6 hover:bg-primary/90"
              render={<a href={bookletHref} />}
            >
              <BookOpen className="size-4" aria-hidden />
              {bookletLabel}
            </ButtonLink>
          </div>
        </div>
      </div>
    </section>
  );
}
