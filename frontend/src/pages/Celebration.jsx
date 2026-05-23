import { useEffect, useRef, useState } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { useReward } from 'partycles'
import { matchesApi } from '../api/matches'
import useSound from '../hooks/useSound'

function Celebration() {
  const { matchId } = useParams()
  const navigate = useNavigate()
  const containerRef = useRef(null)
  const [match, setMatch] = useState(null)
  const [loading, setLoading] = useState(true)
  const { playMatchWon } = useSound()

  const { reward } = useReward(containerRef, 'fireworks', {
    particleCount: 60,
    spread: 120,
    colors: ['#10b981', '#f59e0b', '#ef4444', '#3b82f6', '#f472b6', '#ffffff'],
  })

  useEffect(() => {
    const fetchMatch = async () => {
      try {
        const data = await matchesApi.get(matchId)
        setMatch(data)
      } catch (err) {
        console.error(err)
      } finally {
        setLoading(false)
      }
    }
    fetchMatch()
  }, [matchId])

  // Trigger fireworks and sound on mount
  useEffect(() => {
    const timer = setTimeout(() => {
      reward()
      playMatchWon()
    }, 300)
    return () => clearTimeout(timer)
  }, [reward, playMatchWon])

  if (loading) return <div className="page celebration-page">Cargando...</div>
  if (!match) return <div className="page celebration-page">Partido no encontrado</div>

  const p1Sets = match.sets.filter(s => s.p1 > s.p2).length
  const p2Sets = match.sets.filter(s => s.p2 > s.p1).length
  const winnerName = match.winner === 1 ? match.player1.join(' / ') : match.player2.join(' / ')
  const winnerColor = match.winner === 1 ? 'var(--accent-p1)' : 'var(--accent-p2)'

  return (
    <div className="page celebration-page" ref={containerRef}>
      <div className="celebration-content">
        <h1 style={{ color: winnerColor }}>Ganador</h1>
        <h2 className="celebration-winner">{winnerName}</h2>
        <div className="celebration-score">
          {match.sets.map((set, i) => (
            <span key={i} className="celebration-set">
              {set.p1}-{set.p2}
            </span>
          ))}
        </div>
        <div className="celebration-sets">
          Sets: {p1Sets} — {p2Sets}
        </div>
        <button
          className="btn btn-primary"
          onClick={() => navigate('/')}
        >
          NUEVO PARTIDO
        </button>
      </div>
    </div>
  )
}

export default Celebration
