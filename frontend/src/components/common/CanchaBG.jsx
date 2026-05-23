function CanchaBG() {
  return (
    <div className="cancha-bg" aria-hidden="true">
      <svg viewBox="0 0 220 400" xmlns="http://www.w3.org/2000/svg">
        {/* Outer boundary */}
        <rect x="10" y="10" width="200" height="380" />
        {/* Singles sidelines */}
        <line x1="30" y1="10" x2="30" y2="390" />
        <line x1="190" y1="10" x2="190" y2="390" />
        {/* Service lines */}
        <line x1="10" y1="90" x2="210" y2="90" />
        <line x1="10" y1="310" x2="210" y2="310" />
        {/* Center line */}
        <line x1="110" y1="10" x2="110" y2="390" />
        {/* Center service line */}
        <line x1="30" y1="200" x2="190" y2="200" />
        {/* Net */}
        <line x1="10" y1="200" x2="210" y2="200" strokeWidth="2" />
      </svg>
    </div>
  )
}

export default CanchaBG
