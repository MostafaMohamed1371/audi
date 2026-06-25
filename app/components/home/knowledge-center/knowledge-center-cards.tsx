"use client";

import Image from "next/image";
import { ChevronLeft, ChevronRight } from "lucide-react";
import { useCallback, useState } from "react";
import { cn } from "@/lib/utils";

type CardItem = {
  title: string;
  date: string;
  href: string;
  pdfHref: string;
  image: string;
  icon: string;
};

type Props = {
  items: CardItem[];
  viewIssue: string;
  downloadPdf: string;
  isRtl: boolean;
};

function KnowledgeCard({
  item,
  viewIssue,
  downloadPdf,
  isRtl,
}: {
  item: CardItem;
  viewIssue: string;
  downloadPdf: string;
  isRtl: boolean;
}) {
  return (
    <article className="flex h-full flex-col">
      <div className="relative mb-4 aspect-4/3 overflow-hidden rounded-[20px]">
        <Image
          src={`/knowledgeCenter/${item.image}`}
          alt={item.title}
          fill
          className="object-cover"
          sizes="(max-width: 1024px) 100vw, 33vw"
        />
        <div className="absolute end-4 top-4 h-10 w-10">
          <Image
            src={`/knowledgeCenter/${item.icon}`}
            alt=""
            fill
            className="object-contain"
            sizes="40px"
          />
        </div>
      </div>

      <div
        className={`mb-4 flex items-center justify-between gap-3 ${isRtl ? "flex-row" : "flex-row-reverse"}`}
      >
        <time className="text-xs font-medium tracking-wide text-primary uppercase sm:text-sm">
          {item.date}
        </time>
        <a
          href={item.href}
          className="rounded-md bg-[#e8f4f8] px-3 py-1.5 text-xs font-medium text-primary hover:bg-[#d9edf4] sm:text-sm"
        >
          {viewIssue}
        </a>
      </div>

      <h3
        className={`mb-4 flex-1 text-base font-bold leading-snug text-secondary sm:text-lg ${isRtl ? "text-right" : "text-left"}`}
      >
        {item.title}
      </h3>

      <a
        href={item.pdfHref}
        className={`text-sm font-medium text-[#b8860b] hover:text-[#9a7209] ${isRtl ? "self-start" : "self-end"}`}
      >
        {downloadPdf}
      </a>
    </article>
  );
}

export function KnowledgeCenterCards({
  items,
  viewIssue,
  downloadPdf,
  isRtl,
}: Props) {
  const [current, setCurrent] = useState(0);
  const total = items.length;
  const canNavigate = total > 1;

  const goTo = useCallback(
    (index: number) => {
      if (!canNavigate) return;
      setCurrent((index + total) % total);
    },
    [canNavigate, total],
  );

  const goNext = useCallback(() => goTo(current + 1), [current, goTo]);
  const goPrev = useCallback(() => goTo(current - 1), [current, goTo]);

  const progressStart = isRtl
    ? 100 - ((current + 1) / total) * 100
    : (current / total) * 100;

  return (
    <div>
      <div className="hidden gap-6 lg:grid lg:grid-cols-3 lg:gap-8">
        {items.map((item) => (
          <KnowledgeCard
            key={item.title}
            item={item}
            viewIssue={viewIssue}
            downloadPdf={downloadPdf}
            isRtl={isRtl}
          />
        ))}
      </div>

      <div className="lg:hidden">
        <KnowledgeCard
          item={items[current]!}
          viewIssue={viewIssue}
          downloadPdf={downloadPdf}
          isRtl={isRtl}
        />
      </div>

      {canNavigate ? (
        <div
          className={cn(
            "mt-8 flex items-center gap-4 sm:mt-10",
            isRtl ? "flex-row-reverse" : "flex-row",
          )}
        >
          <div className="flex shrink-0 gap-3">
            <button
              type="button"
              onClick={goPrev}
              aria-label={isRtl ? "السابق" : "Previous"}
              className="flex size-10 items-center justify-center rounded-full border border-primary/30 bg-[#e8f4f8] text-primary transition-colors hover:bg-[#d9edf4] cursor-pointer"
            >
              <ChevronLeft className="size-4 rtl:rotate-180" />
            </button>
            <button
              type="button"
              onClick={goNext}
              aria-label={isRtl ? "التالي" : "Next"}
              className="flex size-10 items-center justify-center rounded-full border border-primary/30 bg-[#e8f4f8] text-primary transition-colors hover:bg-[#d9edf4] cursor-pointer"
            >
              <ChevronRight className="size-4 rtl:rotate-180" />
            </button>
          </div>

          <div className="relative h-px flex-1 bg-primary/20" aria-hidden>
            <div
              className="absolute top-0 h-full bg-primary transition-all duration-500"
              style={{
                width: `${100 / total}%`,
                insetInlineStart: `${progressStart}%`,
              }}
            />
          </div>
        </div>
      ) : null}
    </div>
  );
}
