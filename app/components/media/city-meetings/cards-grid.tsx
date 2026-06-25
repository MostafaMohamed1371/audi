"use client";

import Image from "next/image";
import { Link } from "@/i18n/routing";
import { mediaArticleHref } from "@/lib/hrefs";
import { Calendar, Clock, User } from "lucide-react";
import { cn } from "@/lib/utils";
import { MediaPagination } from "@/app/components/media/media-pagination";

type CityMeetingItem = {
  slug: string;
  title: string;
  image: string;
  authors: string[];
  date: string;
  time: string;
};

type Props = {
  items: CityMeetingItem[];
  watchLabel: string;
  prevLabel: string;
  nextLabel: string;
  isRtl: boolean;
};

export function CityMeetingsCardsGrid({
  items,
  watchLabel,
  prevLabel,
  nextLabel,
  isRtl,
}: Props) {
  return (
    <>
      <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 lg:gap-8">
        {items.map((item) => (
          <article
            key={item.slug}
            className="flex h-full flex-col overflow-hidden rounded-[24px] border border-border/60 bg-white shadow-[1px_1px_18px_0px_#111F4214]"
          >
            <div className="relative aspect-[4/3] overflow-hidden">
              <Image
                src={`/blog/${item.image}`}
                alt={item.title}
                fill
                className="object-cover"
                sizes="(max-width: 1024px) 50vw, 33vw"
              />
            </div>

            <div
              className={cn(
                "flex flex-1 flex-col p-5 sm:p-6",
                isRtl ? "text-start" : "text-start",
              )}
            >
              <h3 className="mb-4 text-base font-bold leading-snug text-secondary sm:text-lg">
                {item.title}
              </h3>

              <div className="mb-5 space-y-2 rounded-xl bg-[#f4f6f8] px-4 py-3">
                <div className="flex items-start gap-2 text-sm text-secondary">
                  <User className="mt-0.5 size-4 shrink-0 text-primary" />
                  <span>{item.authors.join(" • ")}</span>
                </div>
                <div className="flex items-center gap-2 text-sm text-secondary">
                  <Calendar className="size-4 shrink-0 text-primary" />
                  <span>{item.date}</span>
                </div>
                <div className="flex items-center gap-2 text-sm text-secondary">
                  <Clock className="size-4 shrink-0 text-primary" />
                  <span>{item.time}</span>
                </div>
              </div>

              <Link
                href={mediaArticleHref(item.slug, "cityMeetings")}
                className="mt-auto inline-flex w-full items-center justify-center rounded-full bg-primary px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-primary/90"
              >
                {watchLabel}
              </Link>
            </div>
          </article>
        ))}
      </div>

      <MediaPagination
        currentPage={1}
        totalPages={1}
        prevLabel={prevLabel}
        nextLabel={nextLabel}
        isRtl={isRtl}
      />
    </>
  );
}
