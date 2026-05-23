import { useState, useEffect, useCallback } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { useTheme } from '../context/ThemeContext'
import { matchesApi } from '../api/matches'

function Match() {
  const { matchId } = useParams()
  const navigate = useNavigate()
  const { theme, toggleTheme } = useTheme()
  const [match, setMatch] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [shareVisible, setShareVisible] = useState(true)

  const fetchMatch = useCallback(async () => {
    try {
      const data = await matchesApi.get(matchId)
      setMatch(data)
      // Hide share button after first point
      if (data.current_score.p1 > 0 || data.current_score.p2 > 0) {
        setShareVisible(false)
      }
      // Redirect to celebration if match completed
      if (data.status === 'completed') {
        navigate(`/match/${matchId}/celebration`)
      }
    } catch (err) {
      setError(err.message)
    } finally {
      setLoading(false)
    }
  }, [matchId, navigate])

  useEffect(() => {
    fetchMatch()
    const interval = setInterval(fetchMatch, 3000)
    return () => clearInterval(interval)
  }, [fetchMatch])

  const handleScore = async (player) => {
    try {
      const updated = await matchesApi.score(matchId, player)
      setMatch(updated)
      setShareVisible(false)
      if (updated.status === 'completed') {
        navigate(`/match/${matchId}/celebration`)
      }
    } catch (err) {
      setError(err.message)
    }
  }

  const handleUndo = async (player) => {
    try {
      const updated = await matchesApi.score(matchId, player, true)
      setMatch(updated)
    } catch (err) {
      setError(err.message)
    }
  }

  const handleShare = () => {
    const tvUrl = `${window.location.origin}/match/${matchId}/tv`
    navigator.clipboard.writeText(tvUrl)
  }

  if (loading) return <div className="page loading">Cargando...</div>
  if (error) return <div className="page error">Error: {error}</div>
  if (!match) return <div className="page error">Partido no encontrado</div>

  return (
    <div className={`page match-page ${theme}`}>
      <header className="match-header">
        <div className="live-indicator">
          <span className="live-dot" /> EN VIVO
        </div>
        <div className="sets-bar">
          {match.sets.map((set, i) => (
            <span key={i} className="set-badge">
              SET {i + 1}: {set.p1}-{set.p2}
            </span>
          ))}
          <span className="set-badge current">SET {match.current_set}/{match.sets_to_win * 2 - 1}</span>
        </div>
      </header>

      <main className="match-main">
        <div className="scoreboard">
          <div className="player player-1">
            <div className="player-names">
              {match.player1.join(' / ')}
            </div>
            <div className="score">{match.current_score.p1}</div>
            <div className="server-indicator">
              {match.server === 1 && `Servicio ${match.service_side === 'right' ? '→' : '←'}`}
            </div>
          </div>

          <div className="divider">—</div>

          <div className="player player-2">
            <div className="player-names">
              {match.player2.join(' / ')}
            </div>
            <div className="score">{match.current_score.p2}</div>
            <div className="server-indicator">
              {match.server === 2 && `Servicio ${match.service_side === 'right' ? '→' : '←'}`}
            </div>
          </div>
        </div>

        <div className="controls">
          <div className="control-group">
            <button className="btn btn-score" onClick={() => handleScore(1)}>+</button>
            <button className="btn btn-undo" onClick={() => handleUndo(1)} disabled={match.current_score.p1 === 0}>−</button>
          </div>
          <div className="control-group">
            <button className="btn btn-score" onClick={() => handleScore(2)}>+</button>
            <button className="btn btn-undo" onClick={() => handleUndo(2)} disabled={match.current_score.p2 === 0}>−</button>
          </div>
        </div>

        {shareVisible && (
          <button className="btn btn-share" onClick={handleShare}>
            Compartir URL TV
          </button>
        )}
      </main>

      <footer className="app-footer">
        <button className="theme-toggle" onClick={toggleTheme}>
          {theme === 'dark' ? '☀️' : '🌙'}
        </button>
      </footer>
    </div>
  )
}

export default Match
