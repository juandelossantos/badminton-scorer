import { useEffect, useState } from 'react'

function CanchaBG() {
  const [isLandscape, setIsLandscape] = useState(false)

  useEffect(() => {
    const checkOrientation = () => {
      setIsLandscape(window.innerWidth > window.innerHeight)
    }

    checkOrientation()
    window.addEventListener('resize', checkOrientation)
    window.addEventListener('orientationchange', checkOrientation)

    return () => {
      window.removeEventListener('resize', checkOrientation)
      window.removeEventListener('orientationchange', checkOrientation)
    }
  }, [])

  return (
    <div className="cancha-bg" aria-hidden="true">
      {isLandscape ? (
        // Horizontal cancha for landscape/desktop
        <svg viewBox="0 0 400 220" xmlns="http://www.w3.org/2000/svg">
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
      ) : (
        // Vertical cancha for portrait
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
      )}
    </div>
  )
}

export default CanchaBG
