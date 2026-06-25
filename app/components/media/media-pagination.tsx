"use client";

import { ChevronLeft, ChevronRight } from "lucide-react";
import { cn } from "@/lib/utils";

type Props = {
  currentPage: number;
  totalPages: number;
  prevLabel: string;
  nextLabel: string;
  isRtl: boolean;
};

export function MediaPagination({
  currentPage,
  totalPages,
  prevLabel,
  nextLabel,
  isRtl,
}: Props) {
  if (totalPages <= 1) {
    return (
      <div className="mt-12 flex items-center justify-center gap-3">
        <button
          type="button"
          disabled
          aria-label={prevLabel}
          className="flex size-10 cursor-not-allowed items-center justify-center rounded-full bg-primary/20 text-primary/40"
        >
          <ChevronLeft className="size-4 rtl:rotate-180" />
        </button>

        <span className="flex size-10 items-center justify-center rounded-full bg-primary text-sm font-semibold text-white">
          {currentPage}
        </span>

        <button
          type="button"
          disabled
          aria-label={nextLabel}
          className="flex size-10 cursor-not-allowed items-center justify-center rounded-full bg-primary/20 text-primary/40"
        >
          <ChevronRight className="size-4 rtl:rotate-180" />
        </button>
      </div>
    );
  }

  return (
    <div
      className={cn(
        "mt-12 flex items-center justify-center gap-3",
        isRtl && "flex-row-reverse",
      )}
    >
      <button
        type="button"
        aria-label={prevLabel}
        disabled={currentPage <= 1}
        className="flex size-10 items-center justify-center rounded-full bg-primary text-white transition-opacity hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-30"
      >
        <ChevronLeft className="size-4 rtl:rotate-180" />
      </button>

      {Array.from({ length: totalPages }, (_, index) => index + 1).map(
        (page) => (
          <span
            key={page}
            className={cn(
              "flex size-10 items-center justify-center rounded-full text-sm font-semibold",
              page === currentPage
                ? "bg-primary text-white"
                : "bg-primary/10 text-primary",
            )}
          >
            {page}
          </span>
        ),
      )}

      <button
        type="button"
        aria-label={nextLabel}
        disabled={currentPage >= totalPages}
        className="flex size-10 items-center justify-center rounded-full bg-primary text-white transition-opacity hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-30"
      >
        <ChevronRight className="size-4 rtl:rotate-180" />
      </button>
    </div>
  );
}
