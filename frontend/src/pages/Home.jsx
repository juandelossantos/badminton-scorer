import { useNavigate } from 'react-router-dom'

function Home() {
  const navigate = useNavigate()

  return (
    <div className="page home-page">
      <header className="app-header">
        <h1>BADMINTON SCORER</h1>
        <p className="subtitle">Marcador en tiempo real para bádminton</p>
      </header>

      <main className="home-main">
        <button
          className="btn btn-primary btn-large"
          onClick={() => navigate('/create')}
        >
          NUEVO PARTIDO
        </button>
      </main>

      <footer className="app-footer">
        <p>v1.0 — Badminton Scorer</p>
      </footer>
    </div>
  )
}

export default Home
