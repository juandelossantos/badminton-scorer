import { useEffect } from 'react'
import { useParams, useNavigate } from 'react-router-dom'

function Celebration() {
  const { matchId } = useParams()
  const navigate = useNavigate()

  useEffect(() => {
    // Fireworks animation would go here
  }, [])

  return (
    <div className="page celebration-page">
      <div className="celebration-content">
        <h1>🏸 PARTIDO TERMINADO</h1>
        <button
          className="btn btn-primary btn-large"
          onClick={() => navigate('/')}
        >
          INICIO
        </button>
      </div>
    </div>
  )
}

export default Celebration
