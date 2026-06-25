import { Link } from "@/i18n/routing";
import type { FocusAreaItem } from "@/lib/focus-areas";
import { focusAreaHref } from "@/lib/hrefs";
import { ArrowUpRight } from "lucide-react";
import Image from "next/image";

type Props = {
  area: FocusAreaItem;
  viewMoreLabel: string;
};

export function FocusAreaBanner({ area, viewMoreLabel }: Props) {
  return (
    <Link
      href={focusAreaHref(area.slug)}
      className="group relative block aspect-[1920/654] overflow-hidden shadow-[1px_-71px_93.2px_0px_#00709E14] transition-shadow duration-500 hover:shadow-[1px_-71px_93.2px_0px_#00709E14]"
    >
      <Image
        src={area.listImage}
        alt=""
        fill
        className="object-cover transition-transform duration-500 group-hover:scale-[1.02]"
        sizes="100vw"
      />
      <div
        className="absolute inset-0 bg-[rgba(17,31,66,0.85)] transition-colors duration-500 group-hover:bg-[rgba(17,31,66,0.9)]/50"
        aria-hidden
      />

      <div className="relative z-10 flex h-full items-center justify-between gap-6 px-6 sm:px-10 lg:px-16">
        <div className="flex flex-col items-center gap-2 text-white">
          <div className="flex size-12 shrink-0 items-center justify-center sm:size-14">
            <ArrowUpRight className="size-8 text-white transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 sm:size-10" />
          </div>
          <span className="text-sm font-medium text-white/90 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
            {viewMoreLabel}
          </span>
        </div>

        <div className="relative flex flex-1 items-center justify-center px-4 transition-transform duration-300 group-hover:-translate-y-5">
          <div
            className="pointer-events-none absolute t inset-0 -top-18 mx-auto aspect-square max-h-[200px] max-w-[200px] rounded-full border border-primary/80 backdrop-blur-[6.099999904632568px]"
            aria-hidden
          />
          <h2 className="relative z-10 max-w-xl text-center text-2xl font-bold text-white sm:text-3xl lg:text-4xl">
            {area.title}
          </h2>
        </div>

        <div className="flex size-16 shrink-0 items-center justify-center rounded-full bg-primary sm:size-20 lg:size-[88px]">
          <span className="text-2xl font-bold text-white sm:text-3xl lg:text-4xl">
            {area.number}
          </span>
        </div>
      </div>

      <div className="pointer-events-none absolute inset-x-6 bottom-10 z-10 space-y-5 opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100 sm:inset-x-10 lg:inset-x-16 lg:translate-y-3">
        <p className="mx-auto max-w-5xl text-center text-sm leading-8 text-white sm:text-base lg:text-lg lg:leading-9">
          {area.description}
        </p>
        <div className="flex flex-wrap items-center justify-center gap-3">
          {area.tags.map((tag) => (
            <span
              key={tag}
              className="rounded-full bg-primary px-6 py-2 text-xs font-medium text-white sm:px-8 sm:text-sm"
            >
              {tag}
            </span>
          ))}
        </div>
      </div>
    </Link>
  );
}
