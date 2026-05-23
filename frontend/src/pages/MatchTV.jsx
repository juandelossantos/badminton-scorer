import { useState, useEffect, useCallback } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { matchesApi } from '../api/matches'

function MatchTV() {
  const { matchId } = useParams()
  const navigate = useNavigate()
  const [match, setMatch] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  const fetchMatch = useCallback(async () => {
    try {
      const data = await matchesApi.get(matchId)
      setMatch(data)
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

  if (loading) return <div className="page loading">Cargando...</div>
  if (error) return <div className="page error">Error: {error}</div>
  if (!match) return <div className="page error">Partido no encontrado</div>

  return (
    <div className="page tv-page dark">
      <header className="tv-header">
        <div className="tv-sets-bar">
          {match.sets.map((set, i) => (
            <span key={i} className="tv-set-badge">
              {set.p1}-{set.p2}
            </span>
          ))}
        </div>
        <div className="tv-live">EN VIVO</div>
      </header>

      <main className="tv-main">
        <div className="tv-scoreboard">
          <div className="tv-player tv-player-1">
            <div className="tv-names">{match.player1.join(' / ')}</div>
            <div className="tv-score">{match.current_score.p1}</div>
          </div>
          <div className="tv-divider">—</div>
          <div className="tv-player tv-player-2">
            <div className="tv-names">{match.player2.join(' / ')}</div>
            <div className="tv-score">{match.current_score.p2}</div>
          </div>
        </div>
      </main>

      <footer className="tv-footer">
        <div className="tv-set-info">Set {match.current_set}</div>
        <div className="tv-server">
          {match.server === 1 ? match.player1[0] : match.player2[0]} — {match.service_side === 'right' ? 'Derecha' : 'Izquierda'}
        </div>
      </footer>
    </div>
  )
}

export default MatchTV
