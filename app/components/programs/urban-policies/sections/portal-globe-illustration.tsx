type Props = {
  className?: string;
};

export function PortalGlobeIllustration({ className }: Props) {
  return (
    <svg
      viewBox="0 0 420 420"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      className={className}
      aria-hidden
    >
      <circle
        cx="210"
        cy="210"
        r="168"
        stroke="#00709E"
        strokeOpacity="0.18"
        strokeWidth="1.5"
        strokeDasharray="4 8"
      />
      <ellipse
        cx="210"
        cy="210"
        rx="68"
        ry="168"
        stroke="#00709E"
        strokeOpacity="0.22"
        strokeWidth="1.5"
        strokeDasharray="4 8"
      />
      <ellipse
        cx="210"
        cy="210"
        rx="118"
        ry="168"
        stroke="#00709E"
        strokeOpacity="0.16"
        strokeWidth="1.5"
        strokeDasharray="4 8"
      />
      <ellipse
        cx="210"
        cy="120"
        rx="140"
        ry="42"
        stroke="#00709E"
        strokeOpacity="0.2"
        strokeWidth="1.5"
        strokeDasharray="4 8"
      />
      <ellipse
        cx="210"
        cy="300"
        rx="140"
        ry="42"
        stroke="#00709E"
        strokeOpacity="0.2"
        strokeWidth="1.5"
        strokeDasharray="4 8"
      />
      <ellipse
        cx="210"
        cy="210"
        rx="168"
        ry="42"
        stroke="#00709E"
        strokeOpacity="0.24"
        strokeWidth="1.5"
        strokeDasharray="4 8"
      />
      {[
        [210, 42],
        [210, 378],
        [42, 210],
        [378, 210],
        [120, 120],
        [300, 120],
        [120, 300],
        [300, 300],
        [160, 86],
        [260, 86],
        [160, 334],
        [260, 334],
        [86, 160],
        [334, 160],
        [86, 260],
        [334, 260],
      ].map(([cx, cy], index) => (
        <circle
          key={`${cx}-${cy}-${index}`}
          cx={cx}
          cy={cy}
          r="4"
          fill="#00709E"
          fillOpacity="0.35"
        />
      ))}
    </svg>
  );
}
