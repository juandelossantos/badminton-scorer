import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { matchesApi } from '../api/matches'

function CreateMatch() {
  const navigate = useNavigate()
  const [mode, setMode] = useState('singles')
  const [p1Name, setP1Name] = useState('')
  const [p1Partner, setP1Partner] = useState('')
  const [p2Name, setP2Name] = useState('')
  const [p2Partner, setP2Partner] = useState('')
  const [setsToWin, setSetsToWin] = useState(3)
  const [pointsPerSet, setPointsPerSet] = useState(21)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')

  const handleSubmit = async (e) => {
    e.preventDefault()
    setLoading(true)
    setError('')

    const player1 = mode === 'doubles'
      ? [p1Name.trim(), p1Partner.trim()].filter(Boolean)
      : [p1Name.trim()]

    const player2 = mode === 'doubles'
      ? [p2Name.trim(), p2Partner.trim()].filter(Boolean)
      : [p2Name.trim()]

    if (player1.length === 0 || player2.length === 0) {
      setError('Todos los jugadores deben tener nombre')
      setLoading(false)
      return
    }

    if (mode === 'doubles' && (player1.length !== 2 || player2.length !== 2)) {
      setError('En dobles se requieren 2 jugadores por lado')
      setLoading(false)
      return
    }

    try {
      const match = await matchesApi.create({
        mode,
        player1,
        player2,
        sets_to_win: parseInt(setsToWin),
        points_per_set: parseInt(pointsPerSet),
      })
      navigate(`/match/${match.id}`)
    } catch (err) {
      setError(err.message)
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="page create-page">
      <header className="app-header">
        <h1>NUEVO PARTIDO</h1>
      </header>

      <form className="create-form" onSubmit={handleSubmit}>
        <div className="form-group">
          <label>Modo</label>
          <div className="mode-toggle">
            <button
              type="button"
              className={mode === 'singles' ? 'active' : ''}
              onClick={() => setMode('singles')}
            >
              Individual
            </button>
            <button
              type="button"
              className={mode === 'doubles' ? 'active' : ''}
              onClick={() => setMode('doubles')}
            >
              Dobles
            </button>
          </div>
        </div>

        <div className="form-group">
          <label>Jugador 1</label>
          <input
            type="text"
            placeholder="Nombre"
            value={p1Name}
            onChange={e => setP1Name(e.target.value)}
            required
          />
          {mode === 'doubles' && (
            <input
              type="text"
              placeholder="Compañero"
              value={p1Partner}
              onChange={e => setP1Partner(e.target.value)}
              required
            />
          )}
        </div>

        <div className="form-group">
          <label>Jugador 2</label>
          <input
            type="text"
            placeholder="Nombre"
            value={p2Name}
            onChange={e => setP2Name(e.target.value)}
            required
          />
          {mode === 'doubles' && (
            <input
              type="text"
              placeholder="Compañero"
              value={p2Partner}
              onChange={e => setP2Partner(e.target.value)}
              required
            />
          )}
        </div>

        <div className="form-row">
          <div className="form-group">
            <label>Sets para ganar</label>
            <select value={setsToWin} onChange={e => setSetsToWin(e.target.value)}>
              <option value={1}>1</option>
              <option value={2}>2</option>
              <option value={3}>3</option>
            </select>
          </div>
          <div className="form-group">
            <label>Puntos por set</label>
            <select value={pointsPerSet} onChange={e => setPointsPerSet(e.target.value)}>
              <option value={11}>11</option>
              <option value={15}>15</option>
              <option value={21}>21</option>
            </select>
          </div>
        </div>

        {error && <p className="form-error">{error}</p>}

        <button type="submit" className="btn btn-primary btn-large" disabled={loading}>
          {loading ? 'Creando...' : 'CREAR PARTIDO'}
        </button>
      </form>
    </div>
  )
}

export default CreateMatch
