"use client";

import { Button } from "@/app/components/ui/button";
import { ArrowUp } from "lucide-react";

type FooterBackToTopProps = {
  label: string;
};

export function FooterBackToTop({ label }: FooterBackToTopProps) {
  return (
    <Button
      type="button"
      variant="secondary"
      className="size-[72px] shrink-0 flex-col gap-1 rounded-full bg-white text-primary shadow-none hover:bg-white/95"
      onClick={() => window.scrollTo({ top: 0, behavior: "smooth" })}
    >
      <ArrowUp className="size-5 stroke-[2.5]" />
      <span className="text-[11px] font-semibold leading-none">{label}</span>
    </Button>
  );
}
