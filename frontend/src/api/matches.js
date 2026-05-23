const API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:8000'

async function apiFetch(path, options = {}) {
  const url = `${API_BASE}${path}`
  const res = await fetch(url, {
    headers: {
      'Content-Type': 'application/json',
      ...options.headers,
    },
    ...options,
  })

  const data = await res.json()

  if (!res.ok) {
    throw new Error(data.error || `HTTP ${res.status}`)
  }

  return data
}

export const matchesApi = {
  create: (body) => apiFetch('/api/matches/', {
    method: 'POST',
    body: JSON.stringify(body),
  }),

  get: (id) => apiFetch(`/api/matches/${id}`),

  score: (id, player, undo = false) => apiFetch(`/api/matches/${id}/score`, {
    method: 'PUT',
    body: JSON.stringify({ player, undo }),
  }),

  end: (id, winner) => apiFetch(`/api/matches/${id}/end`, {
    method: 'PUT',
    body: JSON.stringify({ status: 'completed', winner }),
  }),
}
