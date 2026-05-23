import { useState, useEffect, useCallback } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { matchesApi } from '../api/matches'
import CanchaBG from '../components/common/CanchaBG'

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

  if (loading) return <div className="page tv-page loading">Cargando...</div>
  if (error) return <div className="page tv-page error">Error: {error}</div>
  if (!match) return <div className="page tv-page error">Partido no encontrado</div>

  const totalSets = match.sets_to_win * 2 - 1
  const p1SetsWon = match.sets.filter(s => s.p1 > s.p2).length
  const p2SetsWon = match.sets.filter(s => s.p2 > s.p1).length

  return (
    <div className="page tv-page">
      {/* Header */}
      <header className="tv-header">
        <div className="tv-live">EN VIVO</div>
        <div className="tv-sets">
          {match.sets.map((set, i) => (
            <span key={i} className="tv-set-score">
              {set.p1}-{set.p2}
            </span>
          ))}
          <span className="tv-current-set">
            Set {match.current_set}/{totalSets}
          </span>
        </div>
      </header>

      {/* Main Scoreboard */}
      <main className="tv-main">
        <CanchaBG />
        <div className="tv-scoreboard">
          <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: '16px' }}>
            <div className="tv-number tv-number-p1">{match.current_score.p1}</div>
            <div className="tv-player-names">{match.player1.join(' / ')}</div>
          </div>
          <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: '16px' }}>
            <div className="tv-number tv-number-p2">{match.current_score.p2}</div>
            <div className="tv-player-names">{match.player2.join(' / ')}</div>
          </div>
        </div>
      </main>

      {/* Footer */}
      <footer className="tv-footer">
        <div>
          {match.mode === 'doubles' ? 'DOBLES' : 'INDIVIDUAL'} — Al {match.points_per_set}
        </div>
        <div>
          {match.server === 1 ? match.player1[0] : match.player2[0]} — {match.service_side === 'right' ? 'Derecha' : 'Izquierda'}
        </div>
      </footer>
    </div>
  )
}

export default MatchTV
