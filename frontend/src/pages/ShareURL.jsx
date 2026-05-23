import { useLocation, useNavigate } from 'react-router-dom'
import { useTheme } from '../context/ThemeContext'
import { SunIcon, MoonIcon } from '../components/common/Icons'

function ShareURL() {
  const location = useLocation()
  const navigate = useNavigate()
  const { theme, toggleTheme } = useTheme()
  const match = location.state?.match

  if (!match) {
    navigate('/create')
    return null
  }

  const controllerUrl = `${window.location.origin}/match/${match.id}`
  const tvUrl = `${window.location.origin}/match/${match.id}/tv`

  const handleCopy = (url) => {
    navigator.clipboard.writeText(url)
  }

  const handleStart = () => {
    navigate(`/match/${match.id}`)
  }

  return (
    <div className="page share-page">
      <header className="app-header">
        <h1>PARTIDO CREADO</h1>
        <p className="subtitle">Comparte la URL con los espectadores</p>
      </header>

      <main className="share-main">
        {/* URL Controller */}
        <div className="share-section">
          <label className="share-label">URL del Controlador</label>
          <div className="share-url-box">
            <code className="share-url">{controllerUrl}</code>
            <button className="btn btn-copy" onClick={() => handleCopy(controllerUrl)}>
              Copiar
            </button>
          </div>
        </div>

        {/* URL TV */}
        <div className="share-section">
          <label className="share-label">URL para Espectadores (TV)</label>
          <div className="share-url-box">
            <code className="share-url">{tvUrl}</code>
            <button className="btn btn-copy" onClick={() => handleCopy(tvUrl)}>
              Copiar
            </button>
          </div>
        </div>

        {/* Start Button */}
        <button className="btn btn-primary" onClick={handleStart}>
          INICIAR PARTIDO
        </button>
      </main>

      <footer className="app-footer">
        <button className="theme-icon" onClick={toggleTheme} aria-label="Toggle theme">
          {theme === 'dark' ? <SunIcon /> : <MoonIcon />}
        </button>
      </footer>
    </div>
  )
}

export default ShareURL
