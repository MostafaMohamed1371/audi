"use client";

import { cn } from "@/lib/utils";
import { ChevronDown } from "lucide-react";
import { useState } from "react";

type FaqEntry = {
  id: number;
  question: string;
  answer: string;
};

type Props = {
  items: FaqEntry[];
  isRtl: boolean;
};

export function FaqAccordion({ items, isRtl }: Props) {
  const [openId, setOpenId] = useState<number | null>(items[0]?.id ?? null);

  return (
    <div dir={isRtl ? "rtl" : "ltr"} className="mx-auto max-w-3xl space-y-3">
      {items.map((item) => {
        const isOpen = openId === item.id;

        return (
          <div
            key={item.id}
            className="overflow-hidden rounded-2xl border border-border/60 bg-white shadow-[1px_1px_18px_0px_#111F4214]"
          >
            <button
              type="button"
              onClick={() => setOpenId(isOpen ? null : item.id)}
              className="flex w-full items-center justify-between gap-4 px-5 py-4 text-start sm:px-6 sm:py-5"
              aria-expanded={isOpen}
            >
              <span className="text-base font-semibold text-secondary sm:text-lg">
                {item.question}
              </span>
              <ChevronDown
                className={cn(
                  "size-5 shrink-0 text-primary transition-transform",
                  isOpen && "rotate-180",
                )}
                aria-hidden
              />
            </button>

            {isOpen ? (
              <div className="border-t border-border/50 px-5 pb-5 pt-3 sm:px-6 sm:pb-6">
                <p className="text-sm leading-8 text-muted-foreground sm:text-base sm:leading-9">
                  {item.answer}
                </p>
              </div>
            ) : null}
          </div>
        );
      })}
    </div>
  );
}
