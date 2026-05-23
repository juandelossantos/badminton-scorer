function CanchaBG({ variant = 'default' }) {
  const isTv = variant === 'tv'

  return (
    <div className={`cancha-bg ${isTv ? 'cancha-bg-tv' : ''}`} aria-hidden="true">
      {/* Horizontal cancha always — players are left vs right */}
      <svg
        viewBox="0 0 400 220"
        xmlns="http://www.w3.org/2000/svg"
        preserveAspectRatio="xMidYMid meet"
      >
        {/* Outer boundary */}
        <rect x="10" y="10" width="380" height="200" />
        {/* Short service lines */}
        <line x1="90" y1="10" x2="90" y2="210" />
        <line x1="310" y1="10" x2="310" y2="210" />
        {/* Long service lines (doubles) */}
        <line x1="30" y1="10" x2="30" y2="210" />
        <line x1="370" y1="10" x2="370" y2="210" />
        {/* Center line */}
        <line x1="200" y1="10" x2="200" y2="210" />
        {/* Center horizontal line */}
        <line x1="10" y1="110" x2="390" y2="110" />
        {/* Net */}
        <line x1="200" y1="10" x2="200" y2="210" strokeWidth="2" />
      </svg>
    </div>
  )
}

export default CanchaBG
