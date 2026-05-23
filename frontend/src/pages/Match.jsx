import { useState, useEffect, useCallback } from 'react'
import { useParams, useNavigate, useSearchParams } from 'react-router-dom'
import { useTheme } from '../context/ThemeContext'
import { matchesApi } from '../api/matches'
import useSound from '../hooks/useSound'
import CanchaBG from '../components/common/CanchaBG'
import { SunIcon, MoonIcon } from '../components/common/Icons'

function Match() {
  const { matchId } = useParams()
  const navigate = useNavigate()
  const [searchParams] = useSearchParams()
  const controlToken = searchParams.get('token') || ''
  const { theme, toggleTheme } = useTheme()
  const { playPoint, playSetWon, playMatchWon } = useSound()
  const [match, setMatch] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [shareVisible, setShareVisible] = useState(true)

  const fetchMatch = useCallback(async () => {
    try {
      const data = await matchesApi.get(matchId)
      setMatch(data)
      if (data.current_score.p1 > 0 || data.current_score.p2 > 0) {
        setShareVisible(false)
      }
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
    if (!controlToken) {
      setError('Token de control requerido')
      return
    }
    try {
      const prevSet = match?.current_set
      const prevSetsCount = match?.sets?.length || 0
      const updated = await matchesApi.score(matchId, player, controlToken)
      setMatch(updated)
      setShareVisible(false)

      if (updated.status === 'completed') {
        playMatchWon()
        navigate(`/match/${matchId}/celebration`)
      } else if (updated.current_set > prevSet || updated.sets.length > prevSetsCount) {
        playSetWon()
      } else {
        playPoint()
      }
    } catch (err) {
      setError(err.message)
    }
  }

  const handleUndo = async (player) => {
    if (!controlToken) {
      setError('Token de control requerido')
      return
    }
    try {
      const updated = await matchesApi.score(matchId, player, controlToken, true)
      setMatch(updated)
    } catch (err) {
      setError(err.message)
    }
  }

  const handleEndMatch = async () => {
    if (!controlToken) {
      setError('Token de control requerido')
      return
    }
    if (!window.confirm('¿Finalizar partido?')) return
    try {
      // Determine current winner based on sets won
      const p1Sets = match.sets.filter(s => s.p1 > s.p2).length
      const p2Sets = match.sets.filter(s => s.p2 > s.p1).length
      const winner = p1Sets > p2Sets ? 1 : p1Sets < p2Sets ? 2 : null
      await matchesApi.end(matchId, winner, controlToken)
      navigate(`/match/${matchId}/celebration`)
    } catch (err) {
      setError(err.message)
    }
  }

  const handleShare = () => {
    const tvUrl = `${window.location.origin}/watch/${matchId}`
    navigator.clipboard.writeText(tvUrl)
  }

  if (loading) return <div className="page loading">Cargando...</div>
  if (error) return <div className="page error">Error: {error}</div>
  if (!match) return <div className="page error">Partido no encontrado</div>

  const p1SetsWon = match.sets.filter(s => s.p1 > s.p2).length
  const p2SetsWon = match.sets.filter(s => s.p2 > s.p1).length

  // Determine server status text
  const p1IsServer = match.server === 1
  const p1Status = p1IsServer
    ? `Servicio ${match.service_side === 'right' ? '→ Derecha' : '← Izquierda'}`
    : 'Recibe'
  const p2Status = !p1IsServer
    ? `Servicio ${match.service_side === 'right' ? '→ Derecha' : '← Izquierda'}`
    : 'Recibe'

  return (
    <div className="page match-page">
      {/* Header */}
      <header className="match-header">
        <div className="header-live">
          <span className="live-dot" />
          <span className="live-text">En Vivo</span>
        </div>
        <div className="header-mode">
          {match.mode === 'doubles' ? 'Dobles' : 'Individual'}
        </div>
      </header>

      {/* Sets Bar */}
      <div className="sets-bar">
        <span className="sets-label">Sets</span>
        {match.sets.length > 0 ? (
          <>
            <span className="sets-score">
              <span className={p1SetsWon > 0 ? 'sets-won' : ''}>{p1SetsWon}</span>
              <span className="sets-divider">—</span>
              <span className={p2SetsWon > 0 ? 'sets-won' : ''}>{p2SetsWon}</span>
            </span>
          </>
        ) : (
          <span className="sets-score">
            <span>0</span>
            <span className="sets-divider">—</span>
            <span>0</span>
          </span>
        )}
        <span className="current-set-badge">
          Set {match.current_set}
        </span>
      </div>

      {/* Players Row */}
      <div className="players-row">
        <div className="player-info p1">
          <div className="player-names">{match.player1.join(' / ')}</div>
          <div className="player-status">{p1Status}</div>
        </div>
        <div className="vs-mini">VS</div>
        <div className="player-info p2">
          <div className="player-names">{match.player2.join(' / ')}</div>
          <div className="player-status">{p2Status}</div>
        </div>
      </div>

      {/* Scoreboard Area */}
      <div className="scoreboard-area">
        <CanchaBG />
        <div className="scoreboard">
          <div className="score-col">
            <div className="big-number number-p1">
              {match.current_score.p1}
            </div>
          </div>
          <div className="score-col">
            <div className="big-number number-p2">
              {match.current_score.p2}
            </div>
          </div>
        </div>
      </div>

      {/* Controls: + and - per player in same row */}
      <div className="controls">
        <div className="control-half">
          <button className="btn btn-score btn-score-p1" onClick={() => handleScore(1)}>+</button>
          <button className="btn btn-minus" onClick={() => handleUndo(1)} disabled={match.current_score.p1 === 0}>−</button>
        </div>
        <div className="control-half">
          <button className="btn btn-score btn-score-p2" onClick={() => handleScore(2)}>+</button>
          <button className="btn btn-minus" onClick={() => handleUndo(2)} disabled={match.current_score.p2 === 0}>−</button>
        </div>
      </div>

      {/* Share button only when score is 0-0 */}
      {shareVisible && (
        <div style={{ display: 'flex', justifyContent: 'center', gap: '12px', padding: '8px 0', zIndex: 1 }}>
          <button className="btn btn-primary" onClick={handleShare} style={{ width: 'auto', padding: '10px 20px', fontSize: '1.2em' }}>
            Compartir URL
          </button>
        </div>
      )}

      {/* End Match button only when match is actually won per badminton rules */}
      {(p1SetsWon >= match.sets_to_win || p2SetsWon >= match.sets_to_win) && (
        <div style={{ display: 'flex', justifyContent: 'center', padding: '8px 0', zIndex: 1 }}>
          <button className="btn btn-end" onClick={handleEndMatch}>
            Finalizar Partido
          </button>
        </div>
      )}

      {/* Footer Tabs */}
      <footer className="app-footer">
        <button className="footer-tab active">Marcador</button>
        <button className="footer-tab">Detalles</button>
        <button className="theme-icon" onClick={toggleTheme} aria-label="Toggle theme">
          {theme === 'dark' ? <SunIcon className="theme-icon" /> : <MoonIcon className="theme-icon" />}
        </button>
      </footer>
    </div>
  )
}

export default Match
