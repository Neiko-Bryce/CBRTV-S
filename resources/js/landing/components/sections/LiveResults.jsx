import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { MdHowToVote, MdPerson, MdLeaderboard } from 'react-icons/md';
import { HiQuestionMarkCircle, HiStatusOnline } from 'react-icons/hi';

export default function LiveResults() {
    const [elections, setElections] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    const fetchResults = async () => {
        try {
            const response = await fetch('/api/live-results');
            const data = await response.json();
            if (data.success) {
                setElections(data.elections);
                setError(null);
            } else {
                setError('Failed to load results');
            }
        } catch (err) {
            console.error('Error fetching live results:', err);
            setError('Unable to connect to server');
        } finally {
            setLoading(false);
        }
    };

    // Initial fetch and polling every 5 seconds for real-time updates
    useEffect(() => {
        fetchResults();
        const interval = setInterval(fetchResults, 5000);
        return () => clearInterval(interval);
    }, []);

    const ongoingElections = elections.filter(e => e.status === 'ongoing');
    const completedElections = elections.filter(e => e.status === 'completed');
    const hasElections = elections.length > 0;

    // Placeholder bar heights for "Hourly Activity" when empty (like reference dashboard)
    const placeholderBars = [28, 42, 38, 55, 48, 65, 72, 88];

    return (
        <section id="live-results" className="py-16 sm:py-20 lg:py-24 relative overflow-hidden bg-white">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {/* ONE permanent card – dark green like Real-Time Analytics Dashboard reference */}
                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    className="relative rounded-3xl overflow-hidden shadow-xl"
                    style={{
                        background: 'linear-gradient(135deg, #166534 0%, #14532d 50%, #052e16 100%)',
                    }}
                >
                    {/* Subtle dot texture */}
                    <div className="absolute inset-0 opacity-30" style={{
                        backgroundImage: `radial-gradient(circle at 1px 1px, rgba(255,255,255,0.08) 1px, transparent 0)`,
                        backgroundSize: '16px 16px',
                    }} />

                    {loading && (
                        <div className="relative flex flex-col items-center justify-center py-24">
                            <div className="w-12 h-12 border-2 border-white/20 border-t-gov-gold-400 rounded-full animate-spin mb-4" />
                            <p className="text-white/80 text-sm">Loading election results...</p>
                        </div>
                    )}

                    {error && !loading && (
                        <div className="relative text-center py-16 px-6">
                            <HiQuestionMarkCircle className="w-12 h-12 text-red-300 mx-auto mb-3" />
                            <p className="text-white/90">{error}</p>
                        </div>
                    )}

                    {/* Empty state – two-column layout like reference (dark green card, white text, yellow accents) */}
                    {!loading && !error && !hasElections && (
                        <div className="relative grid lg:grid-cols-2 gap-8 lg:gap-10 p-6 sm:p-8 lg:p-10 xl:p-12">
                            {/* Left: Premium-style badge, title, paragraph, bullet list */}
                            <div className="flex flex-col justify-center">
                                <span className="inline-flex w-fit items-center rounded-full bg-gov-gold-400 px-3 py-1 text-xs font-semibold text-gov-green-900 mb-4">
                                    Election Results
                                </span>
                                <h2 className="text-2xl sm:text-3xl lg:text-4xl font-bold text-white leading-tight mb-4">
                                    Recent Election
                                    <br />
                                    <span className="pl-0 lg:pl-2">Results</span>
                                </h2>
                                <p className="text-white/90 text-sm sm:text-base leading-relaxed mb-6 max-w-lg">
                                    Monitor election progress with our live results dashboard. When the admin publishes an election—ongoing or completed—results will appear here. Until then, this space is reserved for transparency.
                                </p>
                                <ul className="space-y-2 text-white/90 text-sm sm:text-base">
                                    {['Live participation metrics', 'Position-by-position breakdowns', 'Transparent vote counts', 'Admin-controlled visibility'].map((item, i) => (
                                        <li key={i} className="flex items-center gap-2">
                                            <span className="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-gov-gold-400 flex-shrink-0" />
                                            {item}
                                        </li>
                                    ))}
                                </ul>
                            </div>
                            {/* Right: nested lighter-green panels (glass effect) – Total Votes, Turnout, Hourly Activity */}
                            <div className="grid grid-cols-2 gap-4 lg:gap-5 content-start">
                                <div className="rounded-xl bg-white/10 backdrop-blur-sm border border-white/10 p-4 sm:p-5">
                                    <p className="text-white/80 text-xs sm:text-sm mb-1">Total Votes</p>
                                    <p className="text-2xl sm:text-3xl font-bold text-white">—</p>
                                </div>
                                <div className="rounded-xl bg-white/10 backdrop-blur-sm border border-white/10 p-4 sm:p-5">
                                    <p className="text-white/80 text-xs sm:text-sm mb-1">Turnout</p>
                                    <p className="text-2xl sm:text-3xl font-bold text-gov-gold-400">—</p>
                                </div>
                                <div className="col-span-2 rounded-xl bg-white/10 backdrop-blur-sm border border-white/10 p-4 sm:p-5">
                                    <p className="text-white/80 text-xs sm:text-sm mb-3">Hourly Activity</p>
                                    <div className="flex items-end gap-1 sm:gap-2 h-24">
                                        {placeholderBars.map((h, i) => (
                                            <motion.div
                                                key={i}
                                                initial={{ height: 0 }}
                                                animate={{ height: `${h}%` }}
                                                transition={{ duration: 0.5, delay: i * 0.05 }}
                                                className="flex-1 rounded-t bg-gov-gold-400 min-h-[8px]"
                                            />
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Elections content – when admin displays elections; same dark green card, inner panels like reference */}
                    {!loading && !error && hasElections && (
                    <div className="relative p-6 sm:p-8 lg:p-10 xl:p-12 space-y-8">
                        {/* Ongoing Elections First */}
                        {ongoingElections.map((election, electionIndex) => (
                            <motion.div
                                key={election.id}
                                initial={{ opacity: 0, y: 30 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.5, delay: electionIndex * 0.1 }}
                                className="bg-white/10 backdrop-blur-sm rounded-2xl p-5 sm:p-6 lg:p-8 border border-white/10 shadow-lg"
                            >
                                {/* Election Header */}
                                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-4 border-b border-white/10">
                                    <div>
                                        <div className="flex items-center gap-2 mb-2">
                                            <span className="inline-flex items-center gap-1.5 bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-semibold px-2.5 py-1 rounded-full">
                                                <HiStatusOnline className="w-3 h-3 animate-pulse" />
                                                LIVE NOW
                                            </span>
                                        </div>
                                        <h3 className="text-xl sm:text-2xl font-bold text-white mb-1">
                                            {election.election_name}
                                        </h3>
                                        {election.organization && (
                                            <p className="text-white/70 text-sm">{election.organization}</p>
                                        )}
                                        {election.started_at && (
                                            <p className="text-white/60 text-xs mt-1">
                                                Started: {election.started_at}
                                            </p>
                                        )}
                                    </div>
                                    <div className="flex-shrink-0">
                                        <span className="inline-flex items-center gap-1.5 bg-white/10 border border-white/20 text-white text-xs font-medium px-3 py-2 rounded-lg">
                                            Results will be revealed soon
                                        </span>
                                    </div>
                                </div>

                                {/* Stats Bar */}
                                <div className="flex flex-wrap gap-4 mb-6">
                                    <div className="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-2 border border-white/10">
                                        <MdPerson className="w-4 h-4 text-gov-gold-400" />
                                        <span className="text-white/90 text-sm">
                                            <span className="font-semibold text-white">{election.total_voters}</span> votes cast
                                        </span>
                                    </div>
                                    <div className="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-2 border border-white/10">
                                        <MdLeaderboard className="w-4 h-4 text-gov-gold-400" />
                                        <span className="text-white/90 text-sm">
                                            <span className="font-semibold text-white">{election.positions.length}</span> positions
                                        </span>
                                    </div>
                                </div>

                                {/* Positions and Candidates – flat list, no cards; no yellow border/arrow when ongoing */}
                                <div className="space-y-6">
                                    {election.positions.length > 0 ? (
                                        election.positions.map((position) => (
                                            <PositionResults
                                                key={position.position_id}
                                                position={position}
                                                isLive={true}
                                            />
                                        ))
                                    ) : (
                                        <div className="text-center py-8 text-white/60">
                                            <MdHowToVote className="w-12 h-12 mx-auto mb-3 opacity-50" />
                                            <p>No candidates registered yet</p>
                                        </div>
                                    )}
                                </div>
                            </motion.div>
                        ))}

                        {/* Completed Elections */}
                        {completedElections.length > 0 && ongoingElections.length > 0 && (
                            <div className="flex items-center gap-4 mt-8">
                                <div className="flex-1 h-px bg-white/20"></div>
                                <span className="text-white/70 text-sm font-medium">Recently Completed</span>
                                <div className="flex-1 h-px bg-white/20"></div>
                            </div>
                        )}

                        {completedElections.map((election, electionIndex) => (
                            <motion.div
                                key={election.id}
                                initial={{ opacity: 0, y: 30 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.5, delay: electionIndex * 0.1 }}
                                className="bg-white/10 backdrop-blur-sm rounded-2xl p-5 sm:p-6 lg:p-8 border border-white/10 shadow-lg"
                            >
                                {/* Election Header */}
                                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-4 border-b border-white/10">
                                    <div>
                                        <div className="flex items-center gap-2 mb-2">
                                            <span className="inline-flex items-center gap-1.5 bg-gov-gold-500/20 border border-gov-gold-400/30 text-gov-gold-400 text-xs font-semibold px-2.5 py-1 rounded-full">
                                                <span className="text-sm">🏆</span>
                                                RESULTS REVEALED
                                            </span>
                                        </div>
                                        <h3 className="text-xl sm:text-2xl font-bold text-white mb-1">
                                            {election.election_name}
                                        </h3>
                                        {election.organization && (
                                            <p className="text-white/70 text-sm">{election.organization}</p>
                                        )}
                                        <p className="text-white/60 text-xs mt-1">
                                            Election ended: {election.ended_at}
                                        </p>
                                    </div>
                                </div>

                                {/* Stats Bar */}
                                <div className="flex flex-wrap gap-4 mb-6">
                                    <div className="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-2 border border-white/10">
                                        <MdPerson className="w-4 h-4 text-gov-gold-400" />
                                        <span className="text-white/90 text-sm">
                                            <span className="font-semibold text-white">{election.total_voters}</span> total voters
                                        </span>
                                    </div>
                                    <div className="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-2 border border-white/10">
                                        <MdLeaderboard className="w-4 h-4 text-gov-gold-400" />
                                        <span className="text-white/90 text-sm">
                                            <span className="font-semibold text-white">{election.positions.length}</span> positions
                                        </span>
                                    </div>
                                </div>

                                {/* Positions and Candidates – flat list, partylist shown; yellow border/Winner only when done */}
                                <div className="space-y-6">
                                    {election.positions.length > 0 ? (
                                        election.positions.map((position) => (
                                            <PositionResults
                                                key={position.position_id}
                                                position={position}
                                                isLive={false}
                                            />
                                        ))
                                    ) : (
                                        <div className="text-center py-8 text-white/60">
                                            <MdHowToVote className="w-12 h-12 mx-auto mb-3 opacity-50" />
                                            <p>No candidates were registered</p>
                                        </div>
                                    )}
                                </div>
                            </motion.div>
                        ))}

                        {/* Subtle refresh indicator */}
                        <div className="text-center pt-4">
                            <span className="inline-flex items-center gap-1.5 text-white/50 text-xs">
                                <span className="w-1.5 h-1.5 rounded-full bg-gov-gold-400 animate-pulse" />
                                Updates automatically
                            </span>
                        </div>
                    </div>
                    )}
                </motion.div>
            </div>
        </section>
    );
}

// Position block: flat list, no nested cards. Section title + rows only.
function PositionResults({ position, isLive }) {
    const totalVotes = position.total_votes || 0;
    return (
        <div className="space-y-0">
            <div className="border-b border-white/10 pb-2.5 mb-3">
                <h4 className="text-sm font-medium tracking-wide text-white/90 uppercase flex items-center gap-2">
                    <MdHowToVote className="w-4 h-4 text-gov-gold-400/90 flex-shrink-0" aria-hidden />
                    {position.position_name}
                </h4>
            </div>
            <ul className="space-y-0">
                {position.candidates.map((candidate, candIndex) => {
                    const percentage = totalVotes > 0 ? ((candidate.votes_count / totalVotes) * 100).toFixed(1) : 0;
                    const isLeader = candIndex === 0 && totalVotes > 0;
                    const showWinnerStyle = !isLive && isLeader;
                    return (
                        <CandidateRow
                            key={candidate.id}
                            candidate={candidate}
                            percentage={percentage}
                            rank={candIndex + 1}
                            showWinnerStyle={showWinnerStyle}
                            isLive={isLive}
                        />
                    );
                })}
            </ul>
        </div>
    );
}

// One row per candidate: photo, name, partylist (party reminder), votes, bar. No yellow border/arrow when ongoing.
function CandidateRow({ candidate, percentage, rank, showWinnerStyle, isLive }) {
    const isAnonymous = candidate.is_anonymous;
    const partylistName = candidate.partylist_name || null;
    const votesLabel = candidate.votes_count === 1 ? 'vote' : 'votes';
    return (
        <motion.li
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ duration: 0.2 }}
            className={
                'grid grid-cols-[auto_1fr_auto] sm:grid-cols-[auto_1fr_auto_6rem] gap-x-3 gap-y-1.5 sm:gap-x-4 items-center py-3 px-0 border-b border-white/5 last:border-b-0 ' +
                (showWinnerStyle ? 'border-l-2 border-l-gov-gold-400 pl-3 -ml-0 sm:pl-4' : '')
            }
        >
            <div className="row-span-2 sm:row-span-1 self-center">
                {isAnonymous ? (
                    <div className="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0 border border-white/10">
                        <HiQuestionMarkCircle className="w-5 h-5 sm:w-6 sm:h-6 text-white/50" aria-hidden />
                    </div>
                ) : (
                    <div className={'w-11 h-11 sm:w-12 sm:h-12 rounded-full overflow-hidden flex-shrink-0 border ' + (showWinnerStyle ? 'border-gov-gold-400/80' : 'border-white/15')}>
                        {candidate.photo ? (
                            <img src={candidate.photo} alt="" className="w-full h-full object-cover" onError={(e) => { e.target.style.display = 'none'; if (e.target.nextSibling) e.target.nextSibling.classList.remove('hidden'); }} />
                        ) : null}
                        <div className={'w-full h-full bg-gov-green-700 flex items-center justify-center ' + (candidate.photo ? 'hidden' : '')}>
                            <span className="text-sm font-semibold text-white">{candidate.name?.charAt(0)?.toUpperCase() || '?'}</span>
                        </div>
                    </div>
                )}
            </div>
            <div className="min-w-0 flex-1">
                <div className="flex items-center gap-2 flex-wrap min-w-0">
                    <span className={'text-sm font-semibold text-white break-words line-clamp-2 sm:line-clamp-1 sm:truncate ' + (showWinnerStyle ? 'text-gov-gold-400' : '')} title={candidate.name}>
                        {candidate.name}
                    </span>
                    {!isLive && !isAnonymous && rank <= 3 && (
                        <span className={
                            'text-[10px] sm:text-xs font-semibold px-1.5 py-0.5 rounded flex-shrink-0 ' +
                            (rank === 1 ? 'bg-gov-gold-500/90 text-gov-green-900' : rank === 2 ? 'bg-white/15 text-white' : 'bg-amber-600/70 text-white')
                        }>
                            {rank === 1 ? 'Winner' : rank === 2 ? '2nd' : '3rd'}
                        </span>
                    )}
                </div>
                {partylistName && (
                    <p className="text-xs text-white/50 mt-0.5 truncate" title={partylistName}>Partylist: {partylistName}</p>
                )}
            </div>
            <div className="text-right col-start-3 row-span-2 sm:row-span-1 sm:col-start-3 self-center">
                <p className="text-sm font-medium text-white/90 tabular-nums">{candidate.votes_count.toLocaleString()} {votesLabel}</p>
                <p className="text-xs text-white/50 tabular-nums">{percentage}%</p>
            </div>
            <div className="col-span-3 sm:col-span-1 sm:col-start-4 h-1.5 bg-white/10 rounded-full overflow-hidden self-center">
                <motion.div
                    initial={{ width: 0 }}
                    animate={{ width: `${percentage}%` }}
                    transition={{ duration: 0.5, ease: 'easeOut' }}
                    className={'h-full rounded-full min-w-0 ' + (showWinnerStyle ? 'bg-gov-gold-400/90' : 'bg-white/30')}
                />
            </div>
        </motion.li>
    );
}

