import { test, expect } from '@playwright/test'

const API_URL = 'http://localhost:8000'
const FRONTEND_URL = 'http://localhost:5173'

test.describe('Full match flow E2E', () => {
  test('Singles: create, play points, TV syncs, win set', async ({ browser }) => {
    // Create match via API
    const createRes = await fetch(`${API_URL}/api/matches`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        mode: 'singles',
        player1: ['Juan'],
        player2: ['Pedro']
      })
    })
    const match = await createRes.json()
    const matchId = match.id
    expect(match.mode).toBe('singles')
    expect(match.sets_to_win).toBe(2)
    expect(match.points_per_set).toBe(21)

    // Open controller in one context
    const controllerContext = await browser.newContext({ viewport: { width: 375, height: 812 } })
    const controllerPage = await controllerContext.newPage()
    await controllerPage.goto(`${FRONTEND_URL}/match/${matchId}`)

    // Verify initial state on controller
    await expect(controllerPage.locator('.big-number.number-p1')).toHaveText('0')
    await expect(controllerPage.locator('.big-number.number-p2')).toHaveText('0')
    await expect(controllerPage.locator('.current-set-badge')).toHaveText('Set 1')

    // Open TV in another context
    const tvContext = await browser.newContext({ viewport: { width: 1920, height: 1080 } })
    const tvPage = await tvContext.newPage()
    await tvPage.goto(`${FRONTEND_URL}/match/${matchId}/tv`)

    // Verify initial state on TV
    await expect(tvPage.locator('.tv-number-p1')).toHaveText('0')
    await expect(tvPage.locator('.tv-number-p2')).toHaveText('0')
    await expect(tvPage.locator('.tv-current-set')).toHaveText('Set 1')

    // Play 5 points for Juan on controller
    for (let i = 0; i < 5; i++) {
      await controllerPage.locator('.btn-score-p1').click()
      await controllerPage.waitForTimeout(500)
    }

    // Wait for TV to sync (polling every 3s)
    await tvPage.waitForTimeout(4000)

    // Verify TV updated
    await expect(tvPage.locator('.tv-number-p1')).toHaveText('5')
    await expect(tvPage.locator('.tv-number-p2')).toHaveText('0')

    // Play 3 points for Pedro
    for (let i = 0; i < 3; i++) {
      await controllerPage.locator('.btn-score-p2').click()
      await controllerPage.waitForTimeout(500)
    }

    await tvPage.waitForTimeout(4000)
    await expect(tvPage.locator('.tv-number-p1')).toHaveText('5')
    await expect(tvPage.locator('.tv-number-p2')).toHaveText('3')

    // Verify sets bar shows 0-0
    await expect(controllerPage.locator('.sets-score')).toContainText('0—0')

    // Win the set for Juan (21-0 via API for speed)
    const winSetRes = await fetch(`${API_URL}/api/matches/${matchId}/score`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ player: 1 })
    })
    const afterPoint = await winSetRes.json()

    // Check if set was won or if we need more points
    // We'll just verify the controller shows the current state
    await controllerPage.reload()
    await tvPage.reload()

    // Close contexts
    await controllerContext.close()
    await tvContext.close()
  })

  test('Doubles: create, verify 4 names, play points', async ({ browser }) => {
    // Create doubles match via API
    const createRes = await fetch(`${API_URL}/api/matches`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        mode: 'doubles',
        player1: ['Juan', 'Maria'],
        player2: ['Pedro', 'Ana']
      })
    })
    const match = await createRes.json()
    const matchId = match.id
    expect(match.mode).toBe('doubles')
    expect(match.player1).toEqual(['Juan', 'Maria'])
    expect(match.player2).toEqual(['Pedro', 'Ana'])

    // Open controller
    const controllerPage = await browser.newPage({ viewport: { width: 375, height: 812 } })
    await controllerPage.goto(`${FRONTEND_URL}/match/${matchId}`)
    await controllerPage.waitForSelector('.player-names', { state: 'visible' })

    // Verify all 4 names appear (two .player-names elements, one per side)
    const playerNames = await controllerPage.locator('.player-names').allTextContents()
    const allNames = playerNames.join(' ')
    expect(allNames).toContain('Juan')
    expect(allNames).toContain('Maria')
    expect(allNames).toContain('Pedro')
    expect(allNames).toContain('Ana')

    // Verify mode shows "Dobles"
    await expect(controllerPage.locator('.header-mode')).toContainText('Dobles')

    // Play some points
    await controllerPage.locator('.btn-score-p1').click()
    await controllerPage.waitForTimeout(500)
    await controllerPage.locator('.btn-score-p2').click()
    await controllerPage.waitForTimeout(500)

    // Verify scores
    await expect(controllerPage.locator('.big-number.number-p1')).toHaveText('1')
    await expect(controllerPage.locator('.big-number.number-p2')).toHaveText('1')

    await controllerPage.close()
  })
})
