"use client";

import { cn } from "@/lib/utils";
import { useState } from "react";

type DiagramItem = {
  id: string;
  title: string;
  content?: string;
  columns?: string[];
};

type DiagramRow = {
  type: "split";
  leftId: string;
  rightId: string;
  leftSpan: 1 | 2;
  rightSpan: 1 | 2;
};

type Props = {
  title: string;
  vision: DiagramItem;
  enablers: DiagramItem;
  items: DiagramItem[];
  rows: DiagramRow[];
  placeholder: string;
  isRtl: boolean;
};

const spanClass = {
  1: "lg:col-span-1",
  2: "lg:col-span-2",
} as const;

const TRANSITION =
  "duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] transition-[background-color,box-shadow,border-color,opacity,transform,min-height]";

function AccordionCard({
  item,
  isOpen,
  onToggle,
  placeholder,
  className,
}: {
  item: DiagramItem;
  isOpen: boolean;
  onToggle: () => void;
  placeholder: string;
  className?: string;
}) {
  return (
    <div
      className={cn(
        "overflow-hidden rounded-2xl",
        TRANSITION,
        isOpen
          ? "col-span-full bg-primary text-center shadow-[0_8px_32px_rgba(0,112,158,0.18)]"
          : "border border-primary/15 bg-[#f0f7fa] shadow-[0_4px_24px_rgba(0,112,158,0.08)] hover:bg-[#e4f2f8]",
        className,
      )}
    >
      <button
        type="button"
        onClick={onToggle}
        aria-expanded={isOpen}
        className={cn(
          "w-full cursor-pointer text-center",
          TRANSITION,
          isOpen
            ? "px-6 pt-10 sm:px-8 sm:pt-12"
            : "flex min-h-[112px] items-center justify-center px-6 py-10 hover:scale-[1.01] active:scale-[0.99] sm:min-h-[128px] sm:px-8 sm:py-12",
        )}
      >
        <span
          className={cn(
            "block font-bold",
            TRANSITION,
            isOpen
              ? "text-2xl text-white sm:text-3xl"
              : "text-lg text-secondary sm:text-xl",
          )}
        >
          {item.title}
        </span>
      </button>

      <div
        className={cn(
          "grid transition-[grid-template-rows,opacity] duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]",
          isOpen ? "grid-rows-[1fr] opacity-100" : "grid-rows-[0fr] opacity-0",
        )}
      >
        <div className="overflow-hidden">
          <div
            className={cn(
              "px-6 pb-10 sm:px-8 sm:pb-12",
              TRANSITION,
              isOpen ? "translate-y-0" : "-translate-y-4",
            )}
          >
            {item.columns && item.columns.length > 0 ? (
              <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 lg:gap-4">
                {item.columns.map((column, index) => (
                  <div
                    key={column}
                    className={cn(
                      "flex items-center justify-center px-2 text-sm leading-8 text-white sm:text-[0.95rem] sm:leading-8",
                      TRANSITION,
                      index > 0 && "lg:border-s lg:border-white/30",
                      isOpen ? "translate-y-0 opacity-100" : "translate-y-3 opacity-0",
                    )}
                    style={{
                      transitionDelay: isOpen ? `${100 + index * 70}ms` : "0ms",
                    }}
                  >
                    {column}
                  </div>
                ))}
              </div>
            ) : (
              <p
                className={cn(
                  "mx-auto max-w-4xl text-sm leading-8 text-white/95 sm:text-base sm:leading-8",
                  TRANSITION,
                  isOpen ? "translate-y-0 opacity-100" : "translate-y-3 opacity-0",
                )}
                style={{ transitionDelay: isOpen ? "100ms" : "0ms" }}
              >
                {item.content || placeholder}
              </p>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}

export function StrategyDiagramSection({
  title,
  vision,
  enablers,
  items,
  rows,
  placeholder,
  isRtl,
}: Props) {
  const [openId, setOpenId] = useState<string | null>(null);
  const itemMap = Object.fromEntries(
    [vision, ...items, enablers].map((item) => [item.id, item]),
  );

  const toggle = (id: string) => {
    setOpenId((current) => (current === id ? null : id));
  };

  return (
    <section dir={isRtl ? "rtl" : "ltr"} className="bg-white pb-16 sm:pb-20 lg:pb-24">
      <div className="mx-auto max-w-7xl px-4 sm:px-6">
        <h2 className="mb-8 text-center text-2xl font-bold text-secondary sm:mb-10 sm:text-3xl">
          {title}
        </h2>

        <div className="flex flex-col gap-4 sm:gap-5">
          <AccordionCard
            item={vision}
            isOpen={openId === vision.id}
            onToggle={() => toggle(vision.id)}
            placeholder={placeholder}
          />

          {rows.map((row, index) => {
            const left = itemMap[row.leftId];
            const right = itemMap[row.rightId];
            if (!left || !right) return null;

            const isEqual = row.leftSpan === 1 && row.rightSpan === 1;
            const leftOpen = openId === left.id;
            const rightOpen = openId === right.id;

            return (
              <div
                key={`${row.leftId}-${row.rightId}-${index}`}
                className={cn(
                  "grid gap-4 sm:gap-5",
                  isEqual ? "sm:grid-cols-2" : "lg:grid-cols-3",
                )}
              >
                <AccordionCard
                  item={left}
                  isOpen={leftOpen}
                  onToggle={() => toggle(left.id)}
                  placeholder={placeholder}
                  className={cn(
                    leftOpen && "col-span-full",
                    !leftOpen && !isEqual && spanClass[row.leftSpan],
                  )}
                />

                <AccordionCard
                  item={right}
                  isOpen={rightOpen}
                  onToggle={() => toggle(right.id)}
                  placeholder={placeholder}
                  className={cn(
                    rightOpen && "col-span-full",
                    !rightOpen && !isEqual && spanClass[row.rightSpan],
                  )}
                />
              </div>
            );
          })}

          <AccordionCard
            item={enablers}
            isOpen={openId === enablers.id}
            onToggle={() => toggle(enablers.id)}
            placeholder={placeholder}
          />
        </div>
      </div>
    </section>
  );
}
