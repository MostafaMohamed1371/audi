"use client";

import { cn } from "@/lib/utils";
import { X } from "lucide-react";
import * as React from "react";
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/app/components/ui/dialog";
import {
  Drawer,
  DrawerContent,
  DrawerDescription,
  DrawerFooter,
  DrawerHeader,
  DrawerTitle,
  DrawerTrigger,
} from "@/app/components/ui/drawer";

export interface DialogWrapperProps {
  trigger?: React.ReactNode;
  open?: boolean;
  onOpenChange?: (open: boolean) => void;
  size?: "sm" | "md" | "lg" | "xl";
  header?: {
    mainTitle?: string | React.ReactNode;
    icon?: React.ReactNode;
    iconBgColor?: string;
    subTitle?: string | React.ReactNode;
    description?: string | React.ReactNode;
  };
  content?: React.ReactNode;
  footer?: React.ReactNode;
  className?: string;
  contentClassName?: string;
  closeOnOutsideClick?: boolean;
  scrollableContent?: boolean;
  maxScrollHeight?: string;
  forceDialog?: boolean;
}

function useIsMobile() {
  const [isMobile, setIsMobile] = React.useState(false);

  React.useEffect(() => {
    const checkMobile = () => {
      setIsMobile(window.innerWidth < 768);
    };

    checkMobile();
    window.addEventListener("resize", checkMobile);
    return () => window.removeEventListener("resize", checkMobile);
  }, []);

  return isMobile;
}

export function DialogWrapper({
  trigger,
  open,
  onOpenChange,
  size = "md",
  header,
  content,
  footer,
  className,
  contentClassName,
  closeOnOutsideClick = true,
  scrollableContent = false,
  maxScrollHeight = "350px",
  forceDialog = false,
}: DialogWrapperProps) {
  const isMobile = useIsMobile();
  const shouldUseDialog = forceDialog || !isMobile;
  const scrollContainerRef = React.useRef<HTMLDivElement>(null);
  const [isAtBottom, setIsAtBottom] = React.useState(false);

  React.useEffect(() => {
    if (!scrollableContent) return;
    const container = scrollContainerRef.current;
    if (!container) return;

    const checkScroll = () => {
      const { scrollTop, scrollHeight, clientHeight } = container;
      const atBottom =
        Math.abs(scrollHeight - (scrollTop + clientHeight)) <= 1;
      setIsAtBottom(atBottom);
    };

    checkScroll();
    container.addEventListener("scroll", checkScroll, { passive: true });
    window.addEventListener("resize", checkScroll);

    const resizeObserver = new ResizeObserver(() => {
      setTimeout(checkScroll, 0);
    });
    resizeObserver.observe(container);

    return () => {
      container.removeEventListener("scroll", checkScroll);
      window.removeEventListener("resize", checkScroll);
      resizeObserver.disconnect();
    };
  }, [scrollableContent, content]);

  const sizeClasses = {
    sm: "w-[320px] max-w-[calc(100vw-2rem)]",
    md: "w-[416px] max-w-[calc(100vw-2rem)]",
    lg: "w-[600px] max-w-[calc(100vw-2rem)]",
    xl: "w-[768px] max-w-[calc(100vw-2rem)]",
  };

  const renderScrollableContent = () => (
    <div className="relative overflow-hidden rounded-xl px-2 pb-2">
      <div
        ref={scrollContainerRef}
        className="scrollbar-hide relative overflow-y-auto"
        style={{ maxHeight: maxScrollHeight }}
      >
        <div className={cn(contentClassName)}>{content}</div>
      </div>
      {!isAtBottom ? (
        <div className="pointer-events-none absolute inset-x-0 bottom-0 z-10 h-7 bg-gradient-to-t from-black/10 via-black/7 to-transparent" />
      ) : null}
    </div>
  );

  const renderBodyContent = () => {
    if (!content) return null;

    return scrollableContent ? (
      renderScrollableContent()
    ) : (
      <div className={cn(contentClassName)}>{content}</div>
    );
  };

  const renderHeader = () => {
    if (!header) return null;

    const TitleComponent = isMobile && !forceDialog ? DrawerTitle : DialogTitle;
    const DescriptionComponent =
      isMobile && !forceDialog ? DrawerDescription : DialogDescription;

    return (
      <>
        {header.mainTitle ? (
          <div className="flex items-center justify-between border-b border-solid border-b-[#CDCDCD] px-4 py-3">
            <TitleComponent className="flex-1 text-[18px] font-normal text-[#000] text-center sm:text-start">
              {header.mainTitle}
            </TitleComponent>
            {shouldUseDialog ? (
              <DialogClose className="cursor-pointer rounded-full border border-solid border-[#000] p-1 opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:pointer-events-none">
                <X className="size-4" />
                <span className="sr-only">Close</span>
              </DialogClose>
            ) : null}
          </div>
        ) : null}
        {header.icon ? (
          <div className="flex justify-center">
            <div
              className={cn(
                "flex items-center justify-center rounded-full",
                header.iconBgColor || "bg-green-500",
                header.iconBgColor ? "" : "size-16",
              )}
            >
              {header.icon}
            </div>
          </div>
        ) : null}
        {header.subTitle ? (
          <TitleComponent className="text-lg font-medium">
            {header.subTitle}
          </TitleComponent>
        ) : null}
        {header.description ? (
          <DescriptionComponent className="text-center text-sm text-muted-foreground">
            {header.description}
          </DescriptionComponent>
        ) : null}
      </>
    );
  };

  if (isMobile && !forceDialog) {
    return (
      <Drawer open={open} onOpenChange={onOpenChange}>
        {trigger ? <DrawerTrigger asChild>{trigger}</DrawerTrigger> : null}
        <DrawerContent className={cn("max-h-[96vh]", className)}>
          {header ? (
            <DrawerHeader className="mb-3 space-y-4 p-0 text-center">
              {renderHeader()}
            </DrawerHeader>
          ) : null}
          <div className="p-5 pt-2">{renderBodyContent()}</div>
          {footer ? <DrawerFooter className="gap-2">{footer}</DrawerFooter> : null}
        </DrawerContent>
      </Drawer>
    );
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      {trigger ? <DialogTrigger asChild>{trigger}</DialogTrigger> : null}
      <DialogContent
        className={cn(sizeClasses[size], className)}
        hideCloseButton={!!header?.mainTitle}
        onInteractOutside={(event) => {
          if (!closeOnOutsideClick) {
            event.preventDefault();
          }
        }}
        onEscapeKeyDown={(event) => {
          if (!closeOnOutsideClick) {
            event.preventDefault();
          }
        }}
      >
        {header ? (
          <DialogHeader className="space-y-4 p-0 text-center">
            {renderHeader()}
          </DialogHeader>
        ) : null}
        <div className="p-5 pt-2">
          {renderBodyContent()}
          {footer ? (
            <DialogFooter className="sm:justify-center">{footer}</DialogFooter>
          ) : null}
        </div>
      </DialogContent>
    </Dialog>
  );
}
