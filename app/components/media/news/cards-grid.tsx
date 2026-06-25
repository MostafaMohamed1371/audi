"use client";

import Image from "next/image";
import { Link } from "@/i18n/routing";
import { mediaArticleHref } from "@/lib/hrefs";
import type { MediaArticleCategory } from "@/lib/media";
import { ChevronLeft } from "lucide-react";
import { cn } from "@/lib/utils";
import { MediaPagination } from "@/app/components/media/media-pagination";

type NewsItem = {
  slug: string;
  title: string;
  description: string;
  date: string;
  image: string;
};

type Props = {
  items: NewsItem[];
  readMore: string;
  prevLabel: string;
  nextLabel: string;
  isRtl: boolean;
  category?: MediaArticleCategory;
};

const ITEMS_PER_PAGE = 6;

export function NewsCardsGrid({
  items,
  readMore,
  prevLabel,
  nextLabel,
  isRtl,
  category = "news",
}: Props) {
  return (
    <>
      <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 lg:gap-8">
        {items.slice(0, ITEMS_PER_PAGE).map((item) => (
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
              <time className="mb-3 text-xs font-medium tracking-wide text-primary uppercase sm:text-sm">
                {item.date}
              </time>

              <h3 className="mb-3 flex-1 text-base font-bold leading-snug text-secondary sm:text-lg">
                {item.title}
              </h3>

              <p className="mb-5 line-clamp-3 text-sm leading-7 text-muted-foreground">
                {item.description}
              </p>

              <Link
                href={mediaArticleHref(item.slug, category)}
                className="inline-flex items-center gap-1 text-sm font-medium text-primary hover:text-primary/80"
              >
                {readMore}
                <ChevronLeft className="size-4 rtl:rotate-180" />
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
