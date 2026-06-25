import { TrainingBackButton } from "@/app/components/programs/training/shared/back-button";

type Props = {
  children: React.ReactNode;
  backLabel: string;
  onBack: () => void;
  isRtl?: boolean;
};

export function PanelWrapper({ children, backLabel, onBack, isRtl }: Props) {
  return (
    <div className="px-4 sm:px-6">
      <div className="mx-auto max-w-7xl">
        <div dir={isRtl ? "ltr" : undefined} className={isRtl ? "flex justify-start" : undefined}>
          <TrainingBackButton label={backLabel} onClick={onBack} isRtl={isRtl} />
        </div>
        {children}
      </div>
    </div>
  );
}
