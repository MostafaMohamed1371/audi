"use client";

import { useEffect, useState } from "react";
import { KnowledgeCenterHeader } from "@/app/components/home/knowledge-center/knowledge-center-header";
import { KnowledgeCenterCards } from "@/app/components/home/knowledge-center/knowledge-center-cards";

const knowledgeIcons = ["icon1.png", "icon2.png", "icon3.png"] as const;

type CategorySlide = {
  id?: number;
  slug?: string;
  title: string;
  description: string;
  icon: string;
};

type CardItem = {
  title: string;
  date: string;
  href: string;
  pdfHref: string;
  image: string;
  icon: string;
};

type Props = {
  slides: CategorySlide[];
  categories: { items: CardItem[] }[];
  fallbackItems: CardItem[];
  viewIssue: string;
  downloadPdf: string;
  isRtl: boolean;
};

const INTERVAL_MS = 2000;

export function KnowledgeCenterBlock({
  slides,
  categories,
  fallbackItems,
  viewIssue,
  downloadPdf,
  isRtl,
}: Props) {
  const [activeIndex, setActiveIndex] = useState(0);
  const total = slides.length;

  useEffect(() => {
    if (total <= 1) return;

    const id = setInterval(() => {
      setActiveIndex((prev) => (prev + 1) % total);
    }, INTERVAL_MS);

    return () => clearInterval(id);
  }, [total]);

  const items =
    categories[activeIndex]?.items?.length
      ? categories[activeIndex]!.items
      : fallbackItems;

  return (
    <>
      <KnowledgeCenterHeader
        slides={slides}
        isRtl={isRtl}
        activeIndex={activeIndex}
      />

      <div className="mt-10 sm:mt-16 lg:mt-20">
        <KnowledgeCenterCards
          items={items}
          viewIssue={viewIssue}
          downloadPdf={downloadPdf}
          isRtl={isRtl}
        />
      </div>
    </>
  );
}

export { knowledgeIcons };
