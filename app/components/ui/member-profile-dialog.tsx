"use client";

import { EmployeePortrait } from "@/app/components/ui/employee-portrait";
import { DialogWrapper } from "@/app/components/ui/dialog-wrapper";

type Props = {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  pageTitle: string;
  name: string;
  role: string;
  image: string;
  bio: string;
  isRtl?: boolean;
};

export function MemberProfileDialog({
  open,
  onOpenChange,
  pageTitle,
  name,
  role,
  image,
  bio,
  isRtl = true,
}: Props) {
  return (
    <DialogWrapper
      open={open}
      onOpenChange={onOpenChange}
      size="xl"
      className="max-w-3xl"
      header={{ mainTitle: pageTitle }}
      scrollableContent
      maxScrollHeight="min(60vh, 520px)"
      content={
        <div
          dir="ltr"
          className="grid gap-8 md:grid-cols-[minmax(0,1fr)_220px] md:items-start md:gap-10"
        >
          <div
            dir={isRtl ? "rtl" : "ltr"}
            className="order-2 min-w-0 space-y-4 text-start md:order-1"
          >
            <h2 className="text-2xl font-bold text-primary sm:text-3xl">
              {name}
            </h2>
            <p className="text-base font-medium text-[#b8860b] sm:text-lg">
              {role}
            </p>
            <p className="text-sm leading-8 text-secondary sm:text-base sm:leading-9">
              {bio}
            </p>
          </div>

          <div className="order-1 mx-auto w-full max-w-[220px] shrink-0 md:order-2 md:mx-0">
            <EmployeePortrait image={image} alt={name} sizes="220px" />
          </div>
        </div>
      }
    />
  );
}
