"use client";

import Image from "next/image";
import { Link } from "@/i18n/routing";
import { mediaArticleHref } from "@/lib/hrefs";
import { cn } from "@/lib/utils";
import { MediaPagination } from "@/app/components/media/media-pagination";

type NewsletterItem = {
  slug: string;
  title: string;
  date: string;
  image: string;
  pdfHref: string;
};

type Props = {
  items: NewsletterItem[];
  viewIssue: string;
  downloadPdf: string;
  prevLabel: string;
  nextLabel: string;
  isRtl: boolean;
};

export function NewsletterCardsGrid({
  items,
  viewIssue,
  downloadPdf,
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
              <time className="mb-3 text-xs font-medium tracking-wide text-primary uppercase sm:text-sm">
                {item.date}
              </time>

              <h3 className="mb-4 flex-1 text-base font-bold leading-snug text-secondary sm:text-lg">
                {item.title}
              </h3>

              <a
                href={item.pdfHref}
                className="mb-4 text-sm font-medium text-[#b8860b] hover:text-[#9a7209]"
              >
                {downloadPdf}
              </a>

              <Link
                href={mediaArticleHref(item.slug, "newsletter")}
                className="inline-flex w-full items-center justify-center rounded-full bg-[#e8f4f8] px-6 py-3 text-sm font-semibold text-primary transition-colors hover:bg-[#d9edf4]"
              >
                {viewIssue}
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
