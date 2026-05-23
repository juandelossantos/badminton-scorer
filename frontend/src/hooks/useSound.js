import { useCallback, useRef } from 'react'

/**
 * 8-bit retro sounds via Web Audio API.
 * No external files — everything is synthesized.
 */
function useSound() {
  const ctxRef = useRef(null)

  const getCtx = useCallback(() => {
    if (!ctxRef.current) {
      ctxRef.current = new (window.AudioContext || window.webkitAudioContext)()
    }
    return ctxRef.current
  }, [])

  const playTone = useCallback((freq, duration, type = 'square', volume = 0.1) => {
    const ctx = getCtx()
    const osc = ctx.createOscillator()
    const gain = ctx.createGain()

    osc.type = type
    osc.frequency.setValueAtTime(freq, ctx.currentTime)

    gain.gain.setValueAtTime(volume, ctx.currentTime)
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + duration)

    osc.connect(gain)
    gain.connect(ctx.destination)

    osc.start(ctx.currentTime)
    osc.stop(ctx.currentTime + duration)
  }, [getCtx])

  /** Short blip for scoring a point (Mario coin-like) */
  const playPoint = useCallback(() => {
    const ctx = getCtx()
    const now = ctx.currentTime

    // Two quick ascending tones
    const osc1 = ctx.createOscillator()
    const gain1 = ctx.createGain()
    osc1.type = 'square'
    osc1.frequency.setValueAtTime(880, now)
    gain1.gain.setValueAtTime(0.08, now)
    gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.08)
    osc1.connect(gain1)
    gain1.connect(ctx.destination)
    osc1.start(now)
    osc1.stop(now + 0.08)

    const osc2 = ctx.createOscillator()
    const gain2 = ctx.createGain()
    osc2.type = 'square'
    osc2.frequency.setValueAtTime(1760, now + 0.05)
    gain2.gain.setValueAtTime(0.08, now + 0.05)
    gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.13)
    osc2.connect(gain2)
    gain2.connect(ctx.destination)
    osc2.start(now + 0.05)
    osc2.stop(now + 0.13)
  }, [getCtx])

  /** Alert tone for set won (retro game alert) */
  const playSetWon = useCallback(() => {
    const ctx = getCtx()
    const now = ctx.currentTime
    const notes = [523, 659, 784, 1047] // C5 E5 G5 C6

    notes.forEach((freq, i) => {
      const osc = ctx.createOscillator()
      const gain = ctx.createGain()
      osc.type = 'square'
      osc.frequency.setValueAtTime(freq, now + i * 0.12)
      gain.gain.setValueAtTime(0.1, now + i * 0.12)
      gain.gain.exponentialRampToValueAtTime(0.001, now + i * 0.12 + 0.3)
      osc.connect(gain)
      gain.connect(ctx.destination)
      osc.start(now + i * 0.12)
      osc.stop(now + i * 0.12 + 0.3)
    })
  }, [getCtx])

  /** Celebration fanfare for match won (Mario victory-like) */
  const playMatchWon = useCallback(() => {
    const ctx = getCtx()
    const now = ctx.currentTime
    const melody = [
      { freq: 523, dur: 0.15 },
      { freq: 523, dur: 0.15 },
      { freq: 523, dur: 0.15 },
      { freq: 659, dur: 0.4 },
      { freq: 523, dur: 0.15 },
      { freq: 784, dur: 0.15 },
      { freq: 659, dur: 0.15 },
      { freq: 1047, dur: 0.6 },
    ]

    let t = 0
    melody.forEach(({ freq, dur }) => {
      const osc = ctx.createOscillator()
      const gain = ctx.createGain()
      osc.type = 'square'
      osc.frequency.setValueAtTime(freq, now + t)
      gain.gain.setValueAtTime(0.1, now + t)
      gain.gain.exponentialRampToValueAtTime(0.001, now + t + dur)
      osc.connect(gain)
      gain.connect(ctx.destination)
      osc.start(now + t)
      osc.stop(now + t + dur)
      t += dur
    })
  }, [getCtx])

  return { playPoint, playSetWon, playMatchWon }
}

export default useSound
